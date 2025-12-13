import requests
from bs4 import BeautifulSoup
import csv
import time
from urllib.parse import urljoin, urlparse
import logging

# Set up logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

class WebScraper:
    def __init__(self, base_url, headers=None):
        self.base_url = base_url
        self.session = requests.Session()
        
        # Default headers to avoid being blocked
        default_headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        }
        
        if headers:
            default_headers.update(headers)
        
        self.session.headers.update(default_headers)
    
    def get_page(self, url, timeout=10):
        """Fetch a web page and return BeautifulSoup object"""
        try:
            response = self.session.get(url, timeout=timeout)
            response.raise_for_status()
            return BeautifulSoup(response.content, 'html.parser')
        except requests.RequestException as e:
            logger.error(f"Error fetching {url}: {e}")
            return None
    
    def scrape_quotes(self, max_pages=3):
        """Scrape quotes from quotes.toscrape.com"""
        quotes_data = []
        page = 1
        
        while page <= max_pages:
            url = f"{self.base_url}/page/{page}/"
            logger.info(f"Scraping page {page}: {url}")
            
            soup = self.get_page(url)
            if not soup:
                break
            
            # Find all quote containers
            quotes = soup.find_all('div', class_='quote')
            
            if not quotes:
                logger.info("No more quotes found")
                break
            
            for quote in quotes:
                # Extract quote text
                text = quote.find('span', class_='text')
                text = text.text if text else "N/A"
                
                # Extract author
                author = quote.find('small', class_='author')
                author = author.text if author else "Unknown"
                
                # Extract tags
                tags = quote.find_all('a', class_='tag')
                tags = [tag.text for tag in tags] if tags else []
                
                quote_data = {
                    'text': text,
                    'author': author,
                    'tags': ', '.join(tags),
                    'page': page
                }
                
                quotes_data.append(quote_data)
                logger.info(f"Scraped quote by {author}")
            
            page += 1
            # Be respectful - add delay between requests
            time.sleep(1)
        
        return quotes_data
    
    def save_to_csv(self, data, filename='scraped_data.csv'):
        """Save scraped data to CSV file"""
        if not data:
            logger.warning("No data to save")
            return
        
        try:
            with open(filename, 'w', newline='', encoding='utf-8') as csvfile:
                fieldnames = data[0].keys()
                writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
                
                writer.writeheader()
                for row in data:
                    writer.writerow(row)
                
                logger.info(f"Data saved to {filename}")
                
        except Exception as e:
            logger.error(f"Error saving to CSV: {e}")

def main():
    # Initialize scraper for quotes.toscrape.com
    scraper = WebScraper("http://quotes.toscrape.com")
    
    # Scrape quotes
    logger.info("Starting web scraping...")
    quotes = scraper.scrape_quotes(max_pages=3)
    
    # Save to CSV
    if quotes:
        scraper.save_to_csv(quotes, 'quotes.csv')
        logger.info(f"Scraped {len(quotes)} quotes successfully!")
    else:
        logger.warning("No quotes were scraped")

if __name__ == "__main__":
    main()