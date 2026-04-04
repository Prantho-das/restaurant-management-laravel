"""
Royal Dine - Automated Browser Tester
Tests the frontend like a human - clicking, scrolling, filling forms, etc.
"""

import time
import random
from datetime import datetime
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import (
    NoSuchElementException,
    TimeoutException,
    ElementClickInterceptedException,
)


class RoyalDineTester:
    def __init__(self):
        self.results = []
        self.driver = None
        self.base_url = "http://127.0.0.1:8000"

    def log(self, message, status="INFO"):
        timestamp = datetime.now().strftime("%H:%M:%S")
        log_entry = f"[{timestamp}] [{status}] {message}"
        self.results.append(log_entry)
        print(log_entry)

    def setup_driver(self):
        """Setup Chrome driver with human-like settings"""
        options = Options()
        options.add_argument("--start-maximized")
        options.add_argument("--disable-blink-features=AutomationControlled")
        options.add_experimental_option("excludeSwitches", ["enable-automation"])
        options.add_experimental_option("useAutomationExtension", False)

        self.driver = webdriver.Chrome(options=options)
        self.driver.execute_script(
            "Object.defineProperty(navigator, 'webdriver', {get: () => undefined})"
        )
        self.log("Chrome driver initialized")

    def human_delay(self, min_ms=500, max_ms=2000):
        """Random delay to simulate human behavior"""
        delay = random.randint(min_ms, max_ms) / 1000
        time.sleep(delay)

    def scroll_random(self):
        """Scroll randomly like a human"""
        scroll_amount = random.randint(200, 500)
        self.driver.execute_script(f"window.scrollBy(0, {scroll_amount})")
        self.human_delay(300, 800)

    def visit_page(self, url, page_name):
        """Visit a page and verify it loads"""
        self.log(f"Visiting {page_name}...")
        try:
            self.driver.get(f"{self.base_url}{url}")
            self.human_delay(1000, 2000)

            # Check for page title
            title = self.driver.title
            self.log(f"Page title: {title}", "OK")

            # Check for critical errors
            errors = self.driver.find_elements(
                By.CSS_SELECTOR, ".error, .alert-danger, [class*='error']"
            )
            if errors:
                self.log(f"Found {len(errors)} error elements", "WARN")

            return True
        except Exception as e:
            self.log(f"Error visiting {page_name}: {str(e)}", "ERROR")
            return False

    def test_homepage(self):
        """Test the homepage"""
        self.log("\n" + "=" * 50)
        self.log("TESTING HOMEPAGE")
        self.log("=" * 50)

        self.visit_page("/", "Homepage")

        # Check hero section
        try:
            hero = self.driver.find_element(By.TAG_NAME, "h1")
            self.log(f"Hero text found: {hero.text[:50]}...", "OK")
        except:
            self.log("Hero text not found", "WARN")

        # Check navbar
        try:
            navbar = self.driver.find_element(By.TAG_NAME, "nav")
            self.log("Navbar found", "OK")
        except:
            self.log("Navbar not found", "ERROR")

        # Scroll through page
        for _ in range(3):
            self.scroll_random()

        # Check featured menu section
        try:
            menu_section = self.driver.find_elements(
                By.CSS_SELECTOR, "[class*='menu'], h2"
            )
            self.log(f"Found {len(menu_section)} menu-related elements", "OK")
        except:
            self.log("Menu section check failed", "WARN")

        return True

    def test_menu_page(self):
        """Test the menu page"""
        self.log("\n" + "=" * 50)
        self.log("TESTING MENU PAGE")
        self.log("=" * 50)

        self.visit_page("/menu", "Menu Page")

        # Check for menu items
        try:
            menu_items = self.driver.find_elements(
                By.CSS_SELECTOR, "[class*='card'], [class*='item']"
            )
            self.log(f"Found {len(menu_items)} menu items", "OK")
        except:
            self.log("No menu items found", "WARN")

        # Check category buttons
        try:
            categories = self.driver.find_elements(By.CSS_SELECTOR, "button, a")
            self.log(f"Found {len(categories)} clickable elements", "OK")
        except:
            self.log("No clickable elements found", "WARN")

        self.scroll_random()

        return True

    def test_order_page(self):
        """Test the order page"""
        self.log("\n" + "=" * 50)
        self.log("TESTING ORDER PAGE")
        self.log("=" * 50)

        self.visit_page("/order", "Order Page")

        # Check for products
        try:
            products = self.driver.find_elements(
                By.CSS_SELECTOR, "[class*='product'], [class*='item'], img"
            )
            self.log(f"Found {len(products)} product elements", "OK")
        except:
            self.log("No products found", "WARN")

        # Try clicking add to cart button if exists
        try:
            add_buttons = self.driver.find_elements(
                By.XPATH, "//button[contains(text(), 'Add to Cart')]"
            )
            if add_buttons:
                self.log(f"Found {len(add_buttons)} Add to Cart buttons", "OK")
                # Click one randomly
                if random.random() > 0.5:
                    add_buttons[0].click()
                    self.human_delay(1000, 2000)
                    self.log("Clicked Add to Cart", "OK")
        except Exception as e:
            self.log(f"Could not click Add to Cart: {str(e)}", "WARN")

        self.scroll_random()

        return True

    def test_reservation_page(self):
        """Test the reservation page"""
        self.log("\n" + "=" * 50)
        self.log("TESTING RESERVATION PAGE")
        self.log("=" * 50)

        self.visit_page("/reservation", "Reservation Page")

        # Check for form elements
        try:
            inputs = self.driver.find_elements(By.TAG_NAME, "input")
            self.log(f"Found {len(inputs)} input fields", "OK")
        except:
            self.log("No input fields found", "WARN")

        try:
            selects = self.driver.find_elements(By.TAG_NAME, "select")
            self.log(f"Found {len(selects)} select fields", "OK")
        except:
            self.log("No select fields found", "WARN")

        try:
            buttons = self.driver.find_elements(By.TAG_NAME, "button")
            self.log(f"Found {len(buttons)} buttons", "OK")
        except:
            self.log("No buttons found", "WARN")

        return True

    def test_mobile_sidebar(self):
        """Test mobile sidebar functionality"""
        self.log("\n" + "=" * 50)
        self.log("TESTING MOBILE SIDEBAR")
        self.log("=" * 50)

        # Set mobile viewport
        self.driver.set_window_size(375, 812)  # iPhone X size
        self.human_delay(500)

        self.visit_page("/", "Mobile Homepage")

        # Check for hamburger menu
        try:
            menu_btn = self.driver.find_element(
                By.CSS_SELECTOR, "button, [class*='menu'], [class*='hamburger']"
            )
            menu_btn.click()
            self.human_delay(500, 1000)
            self.log("Clicked mobile menu button", "OK")
        except Exception as e:
            self.log(f"Could not find menu button: {str(e)}", "WARN")

        # Reset to desktop
        self.driver.set_window_size(1920, 1080)
        self.human_delay(500)

        return True

    def test_navigation(self):
        """Test navigation between pages"""
        self.log("\n" + "=" * 50)
        self.log("TESTING NAVIGATION")
        self.log("=" * 50)

        pages = [
            ("/", "Home"),
            ("/menu", "Menu"),
            ("/order", "Order"),
            ("/reservation", "Reservation"),
        ]

        for url, name in pages:
            self.visit_page(url, name)
            self.human_delay(500, 1000)

        return True

    def test_footer(self):
        """Test footer elements"""
        self.log("\n" + "=" * 50)
        self.log("TESTING FOOTER")
        self.log("=" * 50)

        self.visit_page("/", "Homepage for footer test")
        self.scroll_random()

        try:
            footer = self.driver.find_element(By.TAG_NAME, "footer")
            self.log("Footer found", "OK")
        except:
            self.log("Footer not found", "WARN")

        return True

    def run_all_tests(self):
        """Run all tests"""
        self.log("Starting Royal Dine Browser Tests...")
        self.log("=" * 60)

        try:
            self.setup_driver()

            # Run all tests
            self.test_homepage()
            self.test_menu_page()
            self.test_order_page()
            self.test_reservation_page()
            self.test_mobile_sidebar()
            self.test_navigation()
            self.test_footer()

            self.log("\n" + "=" * 60)
            self.log("ALL TESTS COMPLETED!")
            self.log("=" * 60)

        except Exception as e:
            self.log(f"Test failed with error: {str(e)}", "ERROR")

        finally:
            if self.driver:
                self.driver.quit()
                self.log("Browser closed")

        return self.results


def main():
    """Main function to run the tester"""
    print("""
    ╔═══════════════════════════════════════════════════════╗
    ║       Royal Dine - Automated Browser Tester           ║
    ║          Testing frontend like a human                ║
    ╚═══════════════════════════════════════════════════════╝
    """)

    print("NOTE: Make sure the development server is running:")
    print("  php artisan serve --port=8000")
    print()

    input("Press Enter to start testing...")

    tester = RoyalDineTester()
    results = tester.run_all_tests()

    # Save results to file
    with open("test_results.txt", "w") as f:
        f.write("\n".join(results))

    print("\nTest results saved to test_results.txt")


if __name__ == "__main__":
    main()
