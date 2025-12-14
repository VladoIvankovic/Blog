import os
import re
import json
import base64
import subprocess
from datetime import datetime
from dotenv import load_dotenv
import openai
import requests

load_dotenv()

class AutomatedBlogSystem:
    def __init__(self):
        self.openai_client = openai.OpenAI(api_key=os.getenv('OPENAI_API_KEY'))
        self.github_token = os.getenv('GITHUB_TOKEN')
        self.github_username = os.getenv('GITHUB_USERNAME')
        self.github_repo = os.getenv('GITHUB_REPO')
        
        # Blog topics for AI generation
        self.blog_topics = [
            "Python data structures and algorithms",
            "Web scraping with Beautiful Soup",
            "Building REST APIs with Flask",
            "Database operations with SQLite",
            "File processing and automation",
            "Working with JSON and APIs"
        ]
    
    def generate_blog_post(self, topic):
        """Generate a blog post using OpenAI's API"""
        prompt = f"""
        Write a technical blog post about "{topic}". 
        Include:
        1. A clear introduction
        2. Step-by-step explanation
        3. At least one working Python code example
        4. Practical use cases
        5. A conclusion
        
        Format the response in Markdown with proper code blocks using ```python
        Keep it concise but informative (500-800 words).
        """
        
        try:
            response = self.openai_client.chat.completions.create(
                model="gpt-3.5-turbo",
                messages=[{"role": "user", "content": prompt}],
                max_tokens=1500,
                temperature=0.7
            )
            
            return response.choices[0].message.content
        except Exception as e:
            print(f"Error generating blog post: {e}")
            return None
    
    def extract_code_examples(self, blog_content):
        """Extract Python code blocks from blog content"""
        code_pattern = r'```python\n(.*?)\n```'
        code_blocks = re.findall(code_pattern, blog_content, re.DOTALL)
        return code_blocks
    
    def validate_code(self, code):
        """Basic validation of Python code"""
        try:
            compile(code, '<string>', 'exec')
            return True
        except SyntaxError:
            return False
    
    def create_github_file(self, filename, content, commit_message):
        """Create or update a file in GitHub repository"""
        url = f"https://api.github.com/repos/{self.github_username}/{self.github_repo}/contents/{filename}"
        
        headers = {
            'Authorization': f'token {self.github_token}',
            'Accept': 'application/vnd.github.v3+json'
        }
        
        # Check if file exists
        response = requests.get(url, headers=headers)
        
        data = {
            'message': commit_message,
            'content': base64.b64encode(content.encode()).decode()
        }
        
        # If file exists, include SHA for update
        if response.status_code == 200:
            data['sha'] = response.json()['sha']
        
        response = requests.put(url, json=data, headers=headers)
        return response.status_code in [200, 201]
    
    def generate_filename(self, topic):
        """Generate a filename from topic"""
        # Clean topic for filename
        clean_topic = re.sub(r'[^\w\s-]', '', topic.lower())
        clean_topic = re.sub(r'[-\s]+', '-', clean_topic)
        date_str = datetime.now().strftime("%Y-%m-%d")
        return f"posts/{date_str}-{clean_topic}.md"
    
    def run_automation(self):
        """Main automation process"""
        print("🤖 Starting AI Blog Generation System...")
        
        # Select a random topic
        import random
        topic = random.choice(self.blog_topics)
        print(f"📝 Generating post about: {topic}")
        
        # Generate blog post
        blog_content = self.generate_blog_post(topic)
        if not blog_content:
            print("❌ Failed to generate blog post")
            return
        
        # Extract and validate code examples
        code_examples = self.extract_code_examples(blog_content)
        valid_code_count = sum(1 for code in code_examples if self.validate_code(code))
        
        print(f"✅ Generated blog post with {len(code_examples)} code examples ({valid_code_count} valid)")
        
        # Create filename
        filename = self.generate_filename(topic)
        
        # Add metadata header to blog post
        metadata = f"""---
title: "{topic.title()}"
date: {datetime.now().strftime("%Y-%m-%d")}
author: AI Blog System
tags: [python, programming, tutorial]
---

"""
        full_content = metadata + blog_content
        
        # Push to GitHub
        commit_message = f"Add new blog post: {topic}"
        success = self.create_github_file(filename, full_content, commit_message)
        
        if success:
            print(f"🚀 Successfully pushed blog post to GitHub: {filename}")
        else:
            print("❌ Failed to push to GitHub")
        
        # Save locally as backup
        os.makedirs("posts", exist_ok=True)
        local_filename = filename.replace("posts/", "posts/")
        with open(local_filename, 'w') as f:
            f.write(full_content)
        
        print(f"💾 Saved locally: {local_filename}")

if __name__ == "__main__":
    blog_system = AutomatedBlogSystem()
    blog_system.run_automation()