import puppeteer from 'puppeteer';

(async () => {
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();
    await page.setViewport({ width: 1280, height: 800 });

    const capture = async (url, filename) => {
        await page.goto(url, { waitUntil: 'domcontentloaded' });
        // small wait for animations
        await new Promise(r => setTimeout(r, 1000));
        await page.screenshot({ path: `Parkx_FSD_Report/${filename}` });
        console.log(`Captured ${filename}`);
    };

    const login = async (email, password) => {
        await page.goto('http://127.0.0.1:8000/login', { waitUntil: 'domcontentloaded' });
        await page.type('#email', email, { delay: 50 });
        await page.type('#password', password, { delay: 50 });
        await Promise.all([
            page.waitForNavigation({ waitUntil: 'domcontentloaded' }).catch(e => console.log('nav err, ignoring')),
            page.click('button[type="submit"]')
        ]);
        await new Promise(r => setTimeout(r, 1000));
    };

    const logout = async () => {
        const cookies = await page.cookies();
        await page.deleteCookie(...cookies);
    };

    try {
        console.log('Taking public screenshots...');
        await capture('http://127.0.0.1:8000', 'home.png');
        await capture('http://127.0.0.1:8000/login', 'login.png');
        await capture('http://127.0.0.1:8000/register', 'register.png');

        console.log('Logging in as Admin...');
        await login('admin@gmail.com', 'password');
        await capture('http://127.0.0.1:8000/admin', 'admin_dashboard.png');
        await logout();

        console.log('Logging in as Owner...');
        await login('john@gmail.com', 'password');
        await capture('http://127.0.0.1:8000/owner', 'owner_dashboard.png');
        await capture('http://127.0.0.1:8000/owner/vehicle-entry', 'owner_slots.png');
        await logout();

        console.log('Logging in as User...');
        await login('test@gmail.com', 'password');
        await capture('http://127.0.0.1:8000/user', 'user_dashboard.png');
        await capture('http://127.0.0.1:8000/user/book/1', 'user_booking.png');
        await logout();

    } catch (error) {
        console.error('Error taking screenshots:', error);
    } finally {
        await browser.close();
    }
})();
