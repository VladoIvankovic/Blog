# Secure File Upload with AI-Based Content Analysis

A simple web application that provides secure file uploads with AI-powered content analysis to detect potentially harmful or inappropriate content.

## Features

- Secure file upload with validation
- File type and size restrictions
- AI-based content analysis using Google's Perspective API
- Real-time feedback on upload safety
- Clean, responsive web interface

## Setup

1. Install dependencies:
```bash
pip install flask Pillow requests python-magic-bin
```

2. Get a Perspective API key:
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Enable the Perspective API
   - Create an API key
   - Replace `YOUR_PERSPECTIVE_API_KEY` in `app.py`

3. Create upload directory:
```bash
mkdir uploads
```

4. Run the application:
```bash
python app.py
```

5. Open http://localhost:5000 in your browser

## How it works

1. Users upload files through the web interface
2. Server validates file type, size, and performs security checks
3. For text files, content is analyzed using AI for toxicity detection
4. For images, basic metadata analysis is performed
5. Safe files are stored with sanitized names
6. Users receive immediate feedback on upload status

## Supported File Types

- Text files: .txt, .md, .doc, .docx
- Images: .jpg, .jpeg, .png, .gif
- Documents: .pdf

## Security Features

- File type validation
- File size limits
- Filename sanitization
- AI-powered content analysis
- Secure file storage

Note: This is a demo implementation. For production use, implement additional security measures like authentication, rate limiting, and more comprehensive malware scanning.