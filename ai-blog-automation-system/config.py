import os
from datetime import datetime

class BlogConfig:
    """Configuration settings for the automated blog system"""
    
    # AI Generation Settings
    OPENAI_MODEL = "gpt-3.5-turbo"
    MAX_TOKENS = 1500
    TEMPERATURE = 0.7
    
    # Blog Settings
    BLOG_TOPICS = [
        "Python data structures and algorithms",
        "Web scraping with Beautiful Soup", 
        "Building REST APIs with Flask",
        "Database operations with SQLite",
        "File processing and automation",
        "Working with JSON and APIs",
        "Object-oriented programming in Python",
        "Error handling and debugging",
        "Working with CSV and Excel files",
        "Regular expressions in Python"
    ]
    
    # GitHub Settings
    GITHUB_API_BASE = "https://api.github.com"
    DEFAULT_BRANCH = "main"
    POSTS_DIRECTORY = "posts"
    EXAMPLES_DIRECTORY = "examples"
    
    # File Templates
    BLOG_POST_TEMPLATE = """---
title: "{title}"
date: {date}
author: AI Blog System
tags: {tags}
code_examples: {code_count}
---

{content}
"""
    
    # Automation Settings
    POSTS_PER_RUN = 1
    ENABLE_CODE_VALIDATION = True
    ENABLE_GITHUB_PUSH = True
    ENABLE_LOCAL_BACKUP = True
    
    @staticmethod
    def get_post_filename(topic, date=None):
        """Generate standardized filename for blog posts"""
        if date is None:
            date = datetime.now()
        
        # Clean topic for filename
        import re
        clean_topic = re.sub(r'[^\w\s-]', '', topic.lower())
        clean_topic = re.sub(r'[-\s]+', '-', clean_topic)
        date_str = date.strftime("%Y-%m-%d")
        
        return f"{BlogConfig.POSTS_DIRECTORY}/{date_str}-{clean_topic}.md"
    
    @staticmethod
    def get_example_filename(topic, index=0):
        """Generate filename for code examples"""
        import re
        clean_topic = re.sub(r'[^\w\s-]', '', topic.lower())
        clean_topic = re.sub(r'[-\s]+', '-', clean_topic)
        
        return f"{BlogConfig.EXAMPLES_DIRECTORY}/{clean_topic}-example-{index}.py"
    
    @staticmethod
    def validate_environment():
        """Check if all required environment variables are set"""
        required_vars = [
            'OPENAI_API_KEY',
            'GITHUB_TOKEN', 
            'GITHUB_USERNAME',
            'GITHUB_REPO'
        ]
        
        missing_vars = [var for var in required_vars if not os.getenv(var)]
        
        if missing_vars:
            print("❌ Missing required environment variables:")
            for var in missing_vars:
                print(f"   - {var}")
            return False
        
        print("✅ Environment variables validated")
        return True

# Example usage and testing
if __name__ == "__main__":
    # Test configuration
    config = BlogConfig()
    
    print("Blog System Configuration:")
    print(f"Model: {config.OPENAI_MODEL}")
    print(f"Topics: {len(config.BLOG_TOPICS)} configured")
    print(f"Posts directory: {config.POSTS_DIRECTORY}")
    
    # Test filename generation
    test_topic = "Python data structures and algorithms"
    filename = config.get_post_filename(test_topic)
    print(f"Example filename: {filename}")
    
    # Validate environment
    config.validate_environment()