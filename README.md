# OCR Converter Web App

A clean, responsive web application built with PHP 8, HTML5, CSS3, and Vanilla JavaScript that interacts with the Agnes AI API (`agnes-2.5-flash`) to perform high-precision text recognition without heavy client-side frameworks or databases.

---

## Requirements
* PHP 8.0 or higher with `curl` and `fileinfo` extensions enabled.
* A web server environment like XAMPP or cPanel hosting.
* An active Agnes AI API key.

---

## XAMPP Installation
1. Download and install **XAMPP** with PHP 8+.
2. Place the `OCRConverter/` folder inside your `htdocs/` directory (`C:\xampp\htdocs\OCRConverter`).
3. Set your environment variable for the API key:
   - **Windows Environment Variables:** Add a system environment variable named `AGNES_API_KEY` with your API key value. (Restart Apache afterwards).
   - *Alternative (testing only):* Set it directly in Apache `httpd.conf` or your virtual host: `SetEnv AGNES_API_KEY your_api_key_here`.
4. Start Apache via the XAMPP Control Panel.
5. Open your browser and navigate to: `http://localhost/OCRConverter/index.php`.

---

## cPanel Installation
1. Log in to your cPanel account and open **File Manager**.
2. Navigate to your `public_html/` folder and create a folder named `OCRConverter`.
3. Upload all project files (`index.php`, `manual.php`, `app.js`, `styles.css`, `README.md`) into that folder.
4. Set up your environment variable:
   - Go to **MultiPHP INI Editor** or configure `.htaccess` in the folder by adding:
     ```apache
     SetEnv AGNES_API_KEY your_api_key_here
     ```
5. Access your app via `https://yourdomain.com/OCRConverter/index.php`.

---

## Agnes AI Configuration & Security
* **Model:** Uses `agnes-2.5-flash`.
* **Security:** The API key remains strictly server-side inside PHP using `getenv('AGNES_API_KEY')`. It is never exposed, echoed, or hard-coded in frontend JS.
* **Temporary File Handling:** Uploaded images are validated securely using PHP Fileinfo MIME-type verification and cleared out post-processing.# OCRConverter
