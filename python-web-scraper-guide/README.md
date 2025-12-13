# Python Web Scraper

A simple and ethical web scraper built with Python that demonstrates how to extract data from websites using BeautifulSoup and requests.

## Features

- Scrapes quotes from quotes.toscrape.com (a website designed for scraping practice)
- Extracts quote text, author names, and tags
- Saves data to CSV format
- Includes proper error handling and logging
- Implements respectful scraping with delays between requests
- Uses session management for efficient HTTP requests

## Installation

1. Clone or download this project
2. Install the required dependencies:

```bash
pip install -r requirements.txt
```

## Usage

Run the scraper:

```bash
python scraper.py
```

This will:
- Scrape quotes from the first 3 pages of quotes.toscrape.com
- Save the results to `quotes.csv`
- Display progress information in the console

## Project Structure

```
├── scraper.py          # Main scraper class and logic
├── requirements.txt    # Python dependencies
└── README.md          # This file
```

## Code Overview

### WebScraper Class

The main `WebScraper` class provides:

- `__init__()`: Initialize with base URL and headers
- `get_page()`: Fetch and parse web pages
- `scrape_quotes()`: Extract quotes data from multiple pages
- `save_to_csv()`: Export data to CSV format

### Key Features

1. **Respectful Scraping**: Includes delays between requests and proper user-agent headers
2. **Error Handling**: Graceful handling of network errors and missing elements
3. **Logging**: Detailed logging for monitoring scraping progress
4. **Data Export**: Clean CSV output for further analysis

## Example Output

The scraper creates a `quotes.csv` file with the following structure:

```csv
text,author,tags,page
""The world as we have created it is a process of our thinking...",Albert Einstein,"change,deep-thoughts,thinking,world",1
""It is our choices, Harry, that show what we truly are...",J.K. Rowling,"abilities,choices",1
```

## Ethical Considerations

- This scraper is designed to work with quotes.toscrape.com, which is specifically created for scraping practice
- Always check robots.txt and terms of service before scraping any website
- Implement appropriate delays between requests to avoid overloading servers
- Respect rate limits and don't make too many concurrent requests

## Customization

You can easily modify the scraper for other websites by:

1. Changing the `base_url` in the main function
2. Updating the CSS selectors in `scrape_quotes()` method
3. Modifying the data extraction logic for different content types

## Requirements

- Python 3.6+
- requests
- beautifulsoup4
- lxml

## License

This project is for educational purposes. Please use responsibly and ethically.