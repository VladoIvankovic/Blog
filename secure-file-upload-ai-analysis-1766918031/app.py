from flask import Flask, request, render_template, jsonify, flash, redirect
import os
import hashlib
import magic
import requests
import json
from werkzeug.utils import secure_filename
from PIL import Image
import re

app = Flask(__name__)
app.secret_key = 'your-secret-key-change-this'

# Configuration
UPLOAD_FOLDER = 'uploads'
MAX_FILE_SIZE = 10 * 1024 * 1024  # 10MB
ALLOWED_EXTENSIONS = {'txt', 'pdf', 'png', 'jpg', 'jpeg', 'gif', 'md', 'doc', 'docx'}
PERSPECTIVE_API_KEY = 'YOUR_PERSPECTIVE_API_KEY'  # Replace with your actual API key

# Ensure upload folder exists
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

def allowed_file(filename):
    return '.' in filename and filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

def get_file_hash(filepath):
    """Generate SHA-256 hash of file for integrity checking"""
    sha256_hash = hashlib.sha256()
    with open(filepath, "rb") as f:
        for chunk in iter(lambda: f.read(4096), b""):
            sha256_hash.update(chunk)
    return sha256_hash.hexdigest()

def analyze_text_content(text):
    """Analyze text content using Perspective API for toxicity detection"""
    if PERSPECTIVE_API_KEY == 'YOUR_PERSPECTIVE_API_KEY':
        # Mock analysis for demo purposes
        suspicious_words = ['hack', 'malware', 'virus', 'attack', 'exploit']
        text_lower = text.lower()
        score = sum(1 for word in suspicious_words if word in text_lower) * 0.2
        return min(score, 1.0)
    
    url = f'https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze?key={PERSPECTIVE_API_KEY}'
    
    data = {
        'comment': {'text': text},
        'requestedAttributes': {
            'TOXICITY': {},
            'SEVERE_TOXICITY': {},
            'THREAT': {}
        }
    }
    
    try:
        response = requests.post(url, data=json.dumps(data))
        result = response.json()
        
        if 'attributeScores' in result:
            toxicity_score = result['attributeScores']['TOXICITY']['summaryScore']['value']
            return toxicity_score
        return 0.0
    except Exception as e:
        print(f"Error analyzing content: {e}")
        return 0.0

def analyze_image_content(filepath):
    """Basic image analysis for suspicious content"""
    try:
        with Image.open(filepath) as img:
            width, height = img.size
            
            # Simple heuristic checks
            risk_score = 0.0
            
            # Check for unusual dimensions that might indicate embedded content
            if width > 5000 or height > 5000:
                risk_score += 0.3
            
            # Check file size vs image dimensions ratio
            file_size = os.path.getsize(filepath)
            expected_size = width * height * 3  # Rough estimate
            if file_size > expected_size * 2:
                risk_score += 0.2
                
            return min(risk_score, 1.0)
    except Exception as e:
        print(f"Error analyzing image: {e}")
        return 0.5  # Return moderate risk if analysis fails

def perform_content_analysis(filepath, filename):
    """Main content analysis function"""
    file_ext = filename.rsplit('.', 1)[1].lower()
    analysis_result = {
        'safe': True,
        'risk_score': 0.0,
        'warnings': []
    }
    
    # Text file analysis
    if file_ext in ['txt', 'md']:
        try:
            with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                content = f.read()
                risk_score = analyze_text_content(content)
                analysis_result['risk_score'] = risk_score
                
                if risk_score > 0.7:
                    analysis_result['safe'] = False
                    analysis_result['warnings'].append('High toxicity/threat level detected')
                elif risk_score > 0.4:
                    analysis_result['warnings'].append('Moderate risk content detected')
                    
        except Exception as e:
            analysis_result['warnings'].append(f'Error reading text file: {str(e)}')
    
    # Image analysis
    elif file_ext in ['png', 'jpg', 'jpeg', 'gif']:
        risk_score = analyze_image_content(filepath)
        analysis_result['risk_score'] = risk_score
        
        if risk_score > 0.6:
            analysis_result['safe'] = False
            analysis_result['warnings'].append('Suspicious image properties detected')
        elif risk_score > 0.3:
            analysis_result['warnings'].append('Image requires manual review')
    
    return analysis_result

@app.route('/')
def index():
    return render_template('upload.html')

@app.route('/upload', methods=['POST'])
def upload_file():
    if 'file' not in request.files:
        return jsonify({'error': 'No file selected'}), 400
    
    file = request.files['file']
    if file.filename == '':
        return jsonify({'error': 'No file selected'}), 400
    
    if file and allowed_file(file.filename):
        # Security checks
        if len(file.filename) > 255:
            return jsonify({'error': 'Filename too long'}), 400
        
        # Sanitize filename
        filename = secure_filename(file.filename)
        if not filename:
            return jsonify({'error': 'Invalid filename'}), 400
        
        # Check file size
        file.seek(0, 2)  # Seek to end
        file_size = file.tell()
        file.seek(0)  # Seek back to beginning
        
        if file_size > MAX_FILE_SIZE:
            return jsonify({'error': 'File too large (max 10MB)'}), 400
        
        if file_size == 0:
            return jsonify({'error': 'Empty file'}), 400
        
        # Save file temporarily for analysis
        filepath = os.path.join(UPLOAD_FOLDER, filename)
        counter = 1
        original_filename = filename
        
        # Handle duplicate filenames
        while os.path.exists(filepath):
            name, ext = os.path.splitext(original_filename)
            filename = f"{name}_{counter}{ext}"
            filepath = os.path.join(UPLOAD_FOLDER, filename)
            counter += 1
        
        file.save(filepath)
        
        # Verify file type using magic numbers
        try:
            file_type = magic.from_file(filepath, mime=True)
            allowed_types = {
                'text/plain', 'application/pdf', 'image/png', 
                'image/jpeg', 'image/gif', 'text/markdown'
            }
            
            if file_type not in allowed_types and not file_type.startswith('image/'):
                os.remove(filepath)
                return jsonify({'error': 'File type not allowed'}), 400
                
        except Exception as e:
            os.remove(filepath)
            return jsonify({'error': 'Could not verify file type'}), 400
        
        # Perform AI-based content analysis
        analysis = perform_content_analysis(filepath, filename)
        
        if not analysis['safe']:
            os.remove(filepath)  # Remove unsafe file
            return jsonify({
                'error': 'File rejected due to security concerns',
                'details': analysis['warnings']
            }), 400
        
        # Generate file hash for integrity
        file_hash = get_file_hash(filepath)
        
        # File passed all checks
        result = {
            'message': 'File uploaded successfully',
            'filename': filename,
            'size': file_size,
            'hash': file_hash,
            'risk_score': analysis['risk_score'],
            'warnings': analysis['warnings']
        }
        
        return jsonify(result), 200
    
    return jsonify({'error': 'File type not allowed'}), 400

if __name__ == '__main__':
    app.run(debug=True)