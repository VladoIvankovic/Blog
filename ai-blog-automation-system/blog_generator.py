import os
import openai
import json
import datetime
from github_manager import GitHubManager

class BlogGenerator:
    def __init__(self):
        openai.api_key = os.getenv('OPENAI_API_KEY')
        self.github_manager = GitHubManager()
        
    def generate_post(self, topic):
        """Generate a blog post with code examples for the given topic"""
        
        prompt = f"""
        Write a technical blog post about {topic}. Include:
        1. A compelling title
        2. Introduction explaining the topic
        3. Working code example with explanations
        4. Conclusion
        
        Format the response as JSON with these fields:
        - title: Blog post title
        - content: Main blog content (markdown format)
        - code: Working code example
        - filename: Suggested filename for the code
        - description: Brief description for GitHub repo
        """
        
        try:
            response = openai.ChatCompletion.create(
                model="gpt-3.5-turbo",
                messages=[
                    {"role": "system", "content": "You are a technical blog writer who creates educational content with working code examples."},
                    {"role": "user", "content": prompt}
                ],
                max_tokens=2000,
                temperature=0.7
            )
            
            # Parse the JSON response
            post_data = json.loads(response.choices[0].message.content)
            
            # Add metadata
            post_data['created_at'] = datetime.datetime.now().isoformat()
            post_data['topic'] = topic
            
            return post_data
            
        except Exception as e:
            print(f"Error generating post: {e}")
            return None
    
    def save_post(self, post_data):
        """Save the blog post as a markdown file"""
        if not post_data:
            return None
            
        # Create filename from title
        filename = post_data['title'].lower().replace(' ', '-').replace(',', '')
        filename = f"{filename}.md"
        
        # Create blog post content
        blog_content = f"""# {post_data['title']}

*Generated on {post_data['created_at']}*

{post_data['content']}

## Code Example

```python
{post_data['code']}
```

---

*This post was automatically generated using AI. The code examples are available on GitHub.*
"""
        
        # Save to file
        os.makedirs('blog_posts', exist_ok=True)
        filepath = os.path.join('blog_posts', filename)
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(blog_content)
        
        print(f"Blog post saved to: {filepath}")
        return filepath
    
    def create_full_post(self, topic):
        """Generate a complete blog post and push code to GitHub"""
        print(f"Generating blog post for topic: {topic}")
        
        # Generate the post
        post_data = self.generate_post(topic)
        if not post_data:
            return None
        
        # Save the blog post
        blog_file = self.save_post(post_data)
        
        # Create GitHub repository with code
        repo_url = self.github_manager.create_repo_with_code(
            post_data['title'],
            post_data['description'],
            post_data['code'],
            post_data['filename']
        )
        
        if repo_url:
            print(f"Code examples available at: {repo_url}")
            
            # Update blog post with GitHub link
            self._add_github_link_to_post(blog_file, repo_url)
        
        return {
            'blog_file': blog_file,
            'repo_url': repo_url,
            'post_data': post_data
        }
    
    def _add_github_link_to_post(self, blog_file, repo_url):
        """Add GitHub repository link to the blog post"""
        if not blog_file or not repo_url:
            return
            
        with open(blog_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace the footer
        updated_content = content.replace(
            "*This post was automatically generated using AI. The code examples are available on GitHub.*",
            f"*This post was automatically generated using AI. [View code examples on GitHub]({repo_url})*"
        )
        
        with open(blog_file, 'w', encoding='utf-8') as f:
            f.write(updated_content)


def main():
    """Example usage"""
    generator = BlogGenerator()
    
    # List of topics to generate posts for
    topics = [
        "Building a simple web scraper with Python",
        "Creating a REST API with Flask",
        "Data visualization with matplotlib"
    ]
    
    for topic in topics:
        try:
            result = generator.create_full_post(topic)
            if result:
                print(f"✅ Successfully created post: {result['blog_file']}")
                print(f"🔗 Repository: {result['repo_url']}")
            print("-" * 50)
        except Exception as e:
            print(f"❌ Error with topic '{topic}': {e}")


if __name__ == "__main__":
    main()