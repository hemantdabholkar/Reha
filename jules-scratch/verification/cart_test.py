from playwright.sync_api import sync_playwright, Page, expect
import re

def cart_full_test(page: Page):
    """
    Tests the full shopping cart workflow:
    1. Add items to cart.
    2. View and update cart.
    3. Checkout.
    4. Verify order in admin panel.
    """

    # 1. Add items to cart
    page.goto("http://localhost:8000/menu.php?category=sweets_snacks")
    # Add 2 Besan Ladoos
    page.locator(".product-card:has-text('Besan Ladoo')").get_by_role("button", name="Add to Cart").click()
    page.locator(".product-card:has-text('Besan Ladoo')").get_by_role("button", name="Add to Cart").click()
    # Add 1 Chakli
    page.locator(".product-card:has-text('Chakli')").get_by_role("button", name="Add to Cart").click()

    # 2. View and update cart
    page.get_by_role("link", name=re.compile("Cart")).click()
    expect(page).to_have_url("http://localhost:8000/cart.php")
    expect(page.get_by_role("heading", name="Shopping Cart")).to_be_visible()

    # Verify quantities
    expect(page.locator("tr:has-text('Besan Ladoo')").get_by_role("spinbutton")).to_have_value("2")
    expect(page.locator("tr:has-text('Chakli')").get_by_role("spinbutton")).to_have_value("1")
    page.screenshot(path="jules-scratch/verification/cart_view.png", full_page=True)

    # 3. Checkout
    page.get_by_role("link", name="Proceed to Checkout").click()
    expect(page).to_have_url("http://localhost:8000/checkout.php")

    # Fill form and place order
    page.get_by_label("Full Name").fill("Cart Tester")
    page.get_by_label("Email Address").fill("tester@example.com")
    page.get_by_role("button", name="Place Order").click()

    # Verify success message
    expect(page.get_by_role("heading", name="Thank you for your order!")).to_be_visible()
    page.screenshot(path="jules-scratch/verification/checkout_success.png", full_page=True)

    # 4. Verify order in admin panel
    # Login
    page.goto("http://localhost:8000/admin/login.php")
    page.get_by_label("Username").fill("admin")
    page.get_by_label("Password").fill("password")
    page.get_by_role("button", name="Login").click()

    # Go to orders page
    page.get_by_role("link", name="Customer Orders").click()
    expect(page.get_by_role("heading", name="Customer Orders")).to_be_visible()
    expect(page.get_by_text("Cart Tester")).to_be_visible()

    # View details of the new order
    page.get_by_role("row", name=re.compile("Cart Tester")).get_by_role("link", name="View Details").click()
    expect(page.get_by_role("heading", name=re.compile("Order Details"))).to_be_visible()

    # Check for correct items
    expect(page.get_by_text("Besan Ladoo")).to_be_visible()
    expect(page.locator("tr:has-text('Besan Ladoo')").get_by_role("cell").nth(1)).to_have_text("2")
    expect(page.get_by_text("Chakli")).to_be_visible()
    expect(page.locator("tr:has-text('Chakli')").get_by_role("cell").nth(1)).to_have_text("1")
    page.screenshot(path="jules-scratch/verification/admin_order_details.png", full_page=True)


def main():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()
        cart_full_test(page)
        browser.close()

if __name__ == "__main__":
    main()
