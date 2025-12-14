# Automated AI Blog System

This project demonstrates how I built an automated blog system that uses AI to generate posts and automatically pushes working code examples to GitHub.

## Features

- AI-powered blog post generation using OpenAI's GPT API
- Automatic code example generation
- GitHub repository creation and code pushing
- Simple web interface for managing posts

## Setup

1. Install dependencies:
```bash
pip install openai requests gitpython markdown
```

2. Set environment variables:
```bash
export OPENAI_API_KEY="your-openai-api-key"
export GITHUB_TOKEN="your-github-token"
export GITHUB_USERNAME="your-github-username"
```

3. Run the blog generator:
```bash
python blog_generator.py
```

## How it works

1. The system generates blog post topics and content using OpenAI's API
2. For technical posts, it creates working code examples
3. Code examples are automatically pushed to a new GitHub repository
4. Blog posts are saved as markdown files with links to the GitHub repos

## Example Usage

```python
from blog_generator import BlogGenerator

generator = BlogGenerator()
post = generator.generate_post("Python web scraping")
generator.create_github_repo(post)
```

## Files

- `blog_generator.py` - Main blog generation logic
- `github_manager.py` - GitHub integration for creating repos and pushing code
- `README.md` - This file

## Requirements

- Python 3.7+
- OpenAI API key
- GitHub personal access token