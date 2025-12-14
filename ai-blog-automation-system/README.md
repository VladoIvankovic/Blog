# AI-Powered Automated Blog System

This project demonstrates an automated blog system that uses AI to generate technical blog posts with working code examples and automatically pushes them to GitHub.

## Features

- AI-powered blog post generation using OpenAI's API
- Automatic code example creation and validation
- GitHub integration for pushing generated content
- Configurable blog topics and themes

## Setup

1. Install dependencies:
```bash
pip install openai requests python-dotenv
```

2. Create a `.env` file with your credentials:
```
OPENAI_API_KEY=your_openai_api_key_here
GITHUB_TOKEN=your_github_personal_access_token
GITHUB_USERNAME=your_github_username
GITHUB_REPO=your_blog_repo_name
```

3. Run the blog generator:
```bash
python blog_generator.py
```

## How it Works

1. **Content Generation**: Uses OpenAI's GPT model to generate blog posts on programming topics
2. **Code Validation**: Extracts and validates Python code examples from generated content
3. **GitHub Integration**: Automatically commits and pushes generated content to your repository

## Example Output

The system generates:
- Markdown blog posts with technical content
- Working Python code examples
- Automatic Git commits with descriptive messages

## Configuration

Edit the `BLOG_TOPICS` list in `blog_generator.py` to customize the types of posts generated.

## License

MIT License