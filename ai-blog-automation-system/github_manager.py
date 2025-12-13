import os
import requests
import tempfile
import shutil
from git import Repo
import time

class GitHubManager:
    def __init__(self):
        self.token = os.getenv('GITHUB_TOKEN')
        self.username = os.getenv('GITHUB_USERNAME')
        self.headers = {
            'Authorization': f'token {self.token}',
            'Accept': 'application/vnd.github.v3+json'
        }
    
    def create_github_repo(self, name, description):
        """Create a new GitHub repository"""
        # Clean repository name
        repo_name = name.lower().replace(' ', '-').replace(',', '').replace(':', '')
        repo_name = f"blog-example-{repo_name}"
        
        # Repository data
        repo_data = {
            'name': repo_name,
            'description': description,
            'private': False,
            'auto_init': True
        }
        
        try:
            response = requests.post(
                'https://api.github.com/user/repos',
                headers=self.headers,
                json=repo_data
            )
            
            if response.status_code == 201:
                repo_info = response.json()
                print(f"✅ Created GitHub repository: {repo_info['html_url']}")
                return repo_info
            else:
                print(f"❌ Failed to create repository: {response.text}")
                return None
                
        except Exception as e:
            print(f"Error creating repository: {e}")
            return None
    
    def push_code_to_repo(self, repo_info, code, filename):
        """Push code to the GitHub repository"""
        repo_url = repo_info['clone_url'].replace('https://', f'https://{self.token}@')
        
        # Create temporary directory
        with tempfile.TemporaryDirectory() as temp_dir:
            try:
                # Clone the repository
                repo = Repo.clone_from(repo_url, temp_dir)
                
                # Wait a moment for repository initialization
                time.sleep(2)
                
                # Create the code file
                code_path = os.path.join(temp_dir, filename)
                with open(code_path, 'w', encoding='utf-8') as f:
                    f.write(code)
                
                # Create a simple README
                readme_content = f"""# {repo_info['name']}

{repo_info['description']}

## Usage

```bash
python {filename}
```

## Code

The main code is in `{filename}`.

---

*This repository was automatically created as part of an AI-generated blog post.*
"""
                
                readme_path = os.path.join(temp_dir, 'README.md')
                with open(readme_path, 'w', encoding='utf-8') as f:
                    f.write(readme_content)
                
                # Add, commit, and push
                repo.git.add([filename, 'README.md'])
                repo.git.commit('-m', f'Add {filename} and README')
                repo.git.push('origin', 'main')
                
                print(f"✅ Code pushed to repository successfully")
                return True
                
            except Exception as e:
                print(f"❌ Error pushing code: {e}")
                return False
    
    def create_repo_with_code(self, title, description, code, filename):
        """Create repository and push code in one step"""
        if not all([self.token, self.username]):
            print("❌ GitHub credentials not configured")
            return None
        
        # Create the repository
        repo_info = self.create_github_repo(title, description)
        if not repo_info:
            return None
        
        # Push the code
        success = self.push_code_to_repo(repo_info, code, filename)
        if success:
            return repo_info['html_url']
        else:
            return None
    
    def list_repositories(self):
        """List all repositories for debugging"""
        try:
            response = requests.get(
                f'https://api.github.com/users/{self.username}/repos',
                headers=self.headers
            )
            
            if response.status_code == 200:
                repos = response.json()
                print(f"Found {len(repos)} repositories:")
                for repo in repos[:5]:  # Show first 5
                    print(f"  - {repo['name']}: {repo['html_url']}")
                return repos
            else:
                print(f"Error listing repositories: {response.text}")
                return []
                
        except Exception as e:
            print(f"Error: {e}")
            return []


def test_github_connection():
    """Test GitHub connection and credentials"""
    manager = GitHubManager()
    
    if not manager.token or not manager.username:
        print("❌ Missing GitHub credentials")
        print("Set GITHUB_TOKEN and GITHUB_USERNAME environment variables")
        return False
    
    try:
        response = requests.get(
            'https://api.github.com/user',
            headers=manager.headers
        )
        
        if response.status_code == 200:
            user_info = response.json()
            print(f"✅ Connected to GitHub as: {user_info['login']}")
            return True
        else:
            print(f"❌ GitHub authentication failed: {response.text}")
            return False
            
    except Exception as e:
        print(f"❌ Error testing GitHub connection: {e}")
        return False


if __name__ == "__main__":
    # Test the connection
    test_github_connection()