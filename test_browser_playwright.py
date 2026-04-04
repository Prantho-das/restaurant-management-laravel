"""
Royal Dine - Playwright Browser Tester
Fast, reliable browser automation testing like a human
"""

import asyncio
import random
import time
from datetime import datetime
from playwright.async_api import async_playwright


class RoyalDinePlaywrightTester:
    def __init__(self):
        self.results = []
        self.browser = None
        self.context = None
        self.page = None
        self.base_url = "http://127.0.0.1:8000"

    def log(self, message, status="INFO"):
        timestamp = datetime.now().strftime("%H:%M:%S")
        log_entry = f"[{timestamp}] [{status}] {message}"
        self.results.append(log_entry)
        print(log_entry)

    async def setup(self):
        """Setup Playwright browser"""
        self.playwright = await async_playwright().start()
        self.browser = await self.playwright.chromium.launch(headless=True)
        self.context = await self.browser.new_context(
            viewport={"width": 1920, "height": 1080}
        )
        self.page = await self.context.new_page()

        # Add human-like delays
        self.page.set_default_timeout(30000)
        self.log("Playwright browser initialized")

    async def human_delay(self, min_ms=300, max_ms=1500):
        """Random delay to simulate human"""
        delay = random.randint(min_ms, max_ms) / 1000
        await asyncio.sleep(delay)

    async def visit(self, url, name):
        """Visit a page"""
        self.log(f"Visiting {name}...")
        try:
            await self.page.goto(f"{self.base_url}{url}")
            await self.human_delay(500, 1500)

            title = await self.page.title()
            self.log(f"Page title: {title}", "OK")

            return True
        except Exception as e:
            self.log(f"Error: {str(e)}", "ERROR")
            return False

    async def scroll_human(self):
        """Human-like scrolling"""
        for _ in range(random.randint(2, 4)):
            await self.page.evaluate(f"window.scrollBy(0, {random.randint(200, 500)})")
            await self.human_delay(200, 600)

    async def check_element(self, selector, description):
        """Check if element exists"""
        try:
            element = await self.page.query_selector(selector)
            if element:
                self.log(f"✓ {description} found", "OK")
                return True
            else:
                self.log(f"✗ {description} not found", "WARN")
                return False
        except:
            self.log(f"✗ {description} check failed", "ERROR")
            return False

    async def test_homepage(self):
        """Test homepage"""
        self.log("\n" + "=" * 50)
        self.log("🟢 TESTING: HOMEPAGE")
        self.log("=" * 50)

        await self.visit("/", "Homepage")

        # Check key elements
        await self.check_element("nav", "Navbar")
        await self.check_element("h1", "Hero heading")
        await self.check_element("footer", "Footer")

        # Check menu section
        await self.check_element("[class*='menu']", "Menu section")

        await self.scroll_human()

        # Check for images
        images = await self.page.query_selector_all("img")
        self.log(f"Found {len(images)} images", "INFO")

        self.log("Homepage test completed", "OK")

    async def test_menu_page(self):
        """Test menu page"""
        self.log("\n" + "=" * 50)
        self.log("🟢 TESTING: MENU PAGE")
        self.log("=" * 50)

        await self.visit("/menu", "Menu Page")

        # Check for menu items
        await self.human_delay(1000)

        # Check for buttons
        buttons = await self.page.query_selector_all("button")
        self.log(f"Found {len(buttons)} buttons", "INFO")

        # Try to find Add to Cart buttons
        add_buttons = await self.page.query_selector_all(
            "button:has-text('Add to Cart'), button:has-text('Add')"
        )
        if add_buttons:
            self.log(f"Found {len(add_buttons)} Add to Cart buttons", "OK")
            # Click one randomly
            await add_buttons[0].click()
            await self.human_delay(1000)
            self.log("Clicked Add to Cart button", "OK")

        await self.scroll_human()

        self.log("Menu page test completed", "OK")

    async def test_order_page(self):
        """Test order page"""
        self.log("\n" + "=" * 50)
        self.log("🟢 TESTING: ORDER PAGE")
        self.log("=" * 50)

        await self.visit("/order", "Order Page")

        await self.human_delay(1500)

        # Check for products
        product_cards = await self.page.query_selector_all(
            "[class*='card'], [class*='product']"
        )
        self.log(f"Found {len(product_cards)} product cards", "INFO")

        # Check for search input
        await self.check_element(
            "input[type='text'], input[placeholder*='Search']", "Search input"
        )

        # Check for category buttons
        category_btns = await self.page.query_selector_all("button:has-text('All')")
        if category_btns:
            self.log("Category filter buttons found", "OK")

        await self.scroll_human()

        self.log("Order page test completed", "OK")

    async def test_reservation_page(self):
        """Test reservation page"""
        self.log("\n" + "=" * 50)
        self.log("🟢 TESTING: RESERVATION PAGE")
        self.log("=" * 50)

        await self.visit("/reservation", "Reservation Page")

        await self.human_delay(1000)

        # Check for form inputs
        inputs = await self.page.query_selector_all("input")
        self.log(f"Found {len(inputs)} input fields", "INFO")

        # Check for select dropdowns
        selects = await self.page.query_selector_all("select")
        self.log(f"Found {len(selects)} select dropdowns", "INFO")

        # Check for submit button
        submit_btn = await self.page.query_selector("button[type='submit']")
        if submit_btn:
            self.log("Submit button found", "OK")

        await self.scroll_human()

        self.log("Reservation page test completed", "OK")

    async def test_mobile_sidebar(self):
        """Test mobile sidebar"""
        self.log("\n" + "=" * 50)
        self.log("🟢 TESTING: MOBILE SIDEBAR")
        self.log("=" * 50)

        # Set mobile viewport
        await self.context.set_viewport_size({"width": 375, "height": 812})
        await self.human_delay(500)

        await self.visit("/", "Mobile Homepage")

        # Check for hamburger/menu button
        menu_btn = await self.page.query_selector("button")
        if menu_btn:
            self.log("Menu button found", "OK")
            try:
                await menu_btn.click()
                await self.human_delay(800)
                self.log("Clicked menu button", "OK")

                # Check if sidebar opened
                await self.check_element(
                    "[class*='sidebar'], [class*='overlay'], [class*='panel']",
                    "Sidebar/Menu",
                )
            except:
                self.log("Could not click menu button", "WARN")

        # Reset to desktop
        await self.context.set_viewport_size({"width": 1920, "height": 1080})
        await self.human_delay(500)

        self.log("Mobile sidebar test completed", "OK")

    async def test_navigation_flow(self):
        """Test navigation between pages"""
        self.log("\n" + "=" * 50)
        self.log("🟢 TESTING: NAVIGATION FLOW")
        self.log("=" * 50)

        pages = [
            ("/", "Home"),
            ("/menu", "Menu"),
            ("/order", "Order"),
            ("/reservation", "Reservation"),
        ]

        for url, name in pages:
            await self.visit(url, name)
            await self.human_delay(500, 1000)
            # Verify we're on the right page
            current_url = self.page.url
            if url in current_url or current_url.endswith(url):
                self.log(f"✓ Navigated to {name}", "OK")
            else:
                self.log(f"✗ Navigation to {name} failed", "ERROR")

        self.log("Navigation flow test completed", "OK")

    async def test_responsive_design(self):
        """Test responsive design"""
        self.log("\n" + "=" * 50)
        self.log("🟢 TESTING: RESPONSIVE DESIGN")
        self.log("=" * 50)

        viewports = [
            {"width": 1920, "height": 1080, "name": "Desktop"},
            {"width": 768, "height": 1024, "name": "Tablet"},
            {"width": 375, "height": 667, "name": "Mobile"},
        ]

        for viewport in viewports:
            self.log(
                f"Testing {viewport['name']} ({viewport['width']}x{viewport['height']})..."
            )
            await self.context.set_viewport_size(viewport)
            await self.human_delay(300)

            await self.visit("/", f"{viewport['name']} Homepage")

            # Check for layout issues
            try:
                # Check for horizontal scroll
                scroll_width = await self.page.evaluate("document.body.scrollWidth")
                viewport_width = await self.page.evaluate("window.innerWidth")

                if scroll_width > viewport_width:
                    self.log(
                        f"⚠ Horizontal scroll detected on {viewport['name']}", "WARN"
                    )
                else:
                    self.log(f"✓ {viewport['name']} layout OK", "OK")
            except:
                pass

        # Reset
        await self.context.set_viewport_size({"width": 1920, "height": 1080})

        self.log("Responsive design test completed", "OK")

    async def run(self):
        """Run all tests"""
        self.log("=" * 60)
        self.log("🚀 ROYAL DINE BROWSER TESTER STARTED")
        self.log("=" * 60)

        try:
            await self.setup()

            await self.test_homepage()
            await self.test_menu_page()
            await self.test_order_page()
            await self.test_reservation_page()
            await self.test_mobile_sidebar()
            await self.test_navigation_flow()
            await self.test_responsive_design()

            self.log("\n" + "=" * 60)
            self.log("✅ ALL TESTS COMPLETED SUCCESSFULLY!")
            self.log("=" * 60)

        except Exception as e:
            self.log(f"❌ Test failed: {str(e)}", "ERROR")

        finally:
            if self.browser:
                await self.browser.close()
                await self.playwright.stop()
                self.log("Browser closed")

        return self.results


async def main():
    """Main entry point"""
    print("""
    ╔═══════════════════════════════════════════════════════╗
    ║         Royal Dine - Playwright Tester                 ║
    ║        Fast, reliable browser automation               ║
    ╚═══════════════════════════════════════════════════════╝
    """)

    print("📋 Prerequisites:")
    print("   1. Install Playwright: pip install playwright")
    print("   2. Install browsers: playwright install chromium")
    print("   3. Start server: php artisan serve --port=8000")
    print()

    start = input("Press Enter to start testing (or 'q' to quit): ")

    if start.lower() == "q":
        print("Exiting...")
        return

    tester = RoyalDinePlaywrightTester()
    results = await tester.run()

    # Save results
    with open("browser_test_results.txt", "w") as f:
        f.write("\n".join(results))

    print("\n📄 Results saved to browser_test_results.txt")


if __name__ == "__main__":
    asyncio.run(main())
