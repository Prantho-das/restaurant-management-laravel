# Royal Dine Browser Tester
# Install dependencies and run tests

# For Selenium version:
pip install selenium webdriver-manager

# For Playwright version (recommended - faster and more reliable):
pip install playwright
playwright install chromium

# To run tests:

# Option 1: Selenium version (slower but works with any browser)
python test_browser.py

# Option 2: Playwright version (faster, recommended)
python test_browser_playwright.py

# IMPORTANT: Make sure the development server is running first:
# php artisan serve --port=8000