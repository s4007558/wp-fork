# Process Evidence Log

This file combines:
1. Debugging records
2. AI (e.g., Copilot/ChatGPT) usage logs

You must maintain this file throughout development.

---

## General Instructions
- Record entries as you work (not at the end)
- Be honest and specific
- Link to commits. Each debugging record must include at least one related GitHub commit 
(using commit hash and URL).
- Superficial or fabricated entries will not receive marks

---

# 🔧 Section 1: Debugging Records

## Bug 1

**Date Identified:**  
30/09/2026

**Date Fixed:**  
30/09/2026

**File:**  
`htdocs/wp-fork/a2/index.php` and directory root

**Related Commit:**  
`c4a18b2` (https://github.com/s4007558/wp/commit/c4a18b2)

**Symptom:**  
The browser rendered raw PHP source code as static plain text / HTML instead of executing server-side logic and database queries. Dynamic includes and database content failed to render.

**Steps to Reproduce:**  
1. Navigate to `http://localhost/wp-fork/a2/index.php`.
2. Notice the PHP opening tags `<?php` and source code appearing as unparsed HTML elements in browser Developer Tools.

**Root Cause:**  
Two days ago, I accidentally merged an older Assignment 1 static website folder directly into the new dynamic Assignment 2 `a2` workspace. This corrupted the nested file structure and introduced conflicting `.htaccess` MIME handlers and static `.html` entry points that overrode Apache's PHP handler configuration, confusing Apache into treating `.php` files as raw text/HTML documents.

**Fix:**  
Completely cleaned the workspace directory tree. Removed legacy static assets and redundant `.html` files, restored the proper `a2` structure according to the assessment brief, and ensured Apache parsed the standard `.php` extension.

**Verification:**  
Reloaded `http://localhost/wp-fork/a2/index.php` via XAMPP. Verified that PHP processed server-side code properly and the output contained dynamic content rather than raw PHP tags.

---

## Bug 2

**Date Identified:**  
02/10/2026

**Date Fixed:**  
02/10/2026

**File:**  
`books.php`

**Related Commit:**  
`7f3b890` (https://github.com/YOUR_STUDENT_ID/wp/commit/7f3b890)

**Symptom:**  
PHP Fatal error: `Fatal error: Uncaught Error: Undefined constant "endline" in C:\xampp\htdocs\wp-fork\a2\books.php on line 44`. The page terminated execution abruptly mid-render right after the title "1984".

**Steps to Reproduce:**  
1. Load `http://localhost/wp-fork/a2/books.php`.
2. Observe execution crash during table generation when looping through book records.

**Root Cause:**  
An invalid closing syntax token (`<?php endline; ?>`) was present directly prior to the `endwhile;` statement in the PHP while loop block. PHP interpreted `endline` as an undefined bareword constant.

**Fix:**  
Removed the erroneous `<?php endline; ?>` statement and retained only the syntactically correct `<?php endwhile; ?>` to cleanly terminate the loop.

**Verification:**  
Reloaded `books.php`. The table rendered without triggering fatal parsing errors.

---

## Bug 3

**Date Identified:**  
02/10/2026

**Date Fixed:**  
02/10/2026

**File:**  
`books.php` and `index.php`

**Related Commit:**  
`e184a29` (https://github.com/YOUR_STUDENT_ID/wp/commit/e184a29)

**Symptom:**  
`Warning: Undefined array key "publish_year"` in `books.php` followed by `Database Error: Unknown column 'publish_year' in 'field list'` in `index.php`.

**Steps to Reproduce:**  
1. Fetch records in `books.php` using `$row['publish_year']`.
2. Observe warning banners and blank table cells where the year should appear.

**Root Cause:**  
Code attempted to query and read from `publish_year`, whereas the actual SQL schema imported for the assessment defines the column as `publication_year`.

**Fix:**  
Updated all query strings, prepared statements, and associative array keys across `books.php`, `index.php`, and `details.php` to reference `publication_year`.

**Verification:**  
Checked the rendered table on `books.php` and confirmed the publication years (e.g., 2020, 2021, 1965, 1949) displayed accurately under the publication year column.

---

## Bug 4

**Date Identified:**  
02/10/2026

**Date Fixed:**  
02/10/2026

**File:**  
`index.php`, `books.php`, `gallery.php`, and `details.php`

**Related Commit:**  
`a953d10` (https://github.com/YOUR_STUDENT_ID/wp/commit/a953d10)

**Symptom:**  
`Database Query Error: Unknown column 'id' in 'field list'` accompanied by `Fatal error: Uncaught TypeError: mysqli_stmt_execute(): Argument #1 ($statement) must be of type mysqli_stmt, bool given`.

**Steps to Reproduce:**  
1. Execute `SELECT id, title, ... FROM books`.
2. `mysqli_prepare()` returns `false`, causing `mysqli_stmt_execute()` to fail with a type error.

**Root Cause:**  
The database table primary key was assumed to be `id`. Diagnostic inspection of the table structure using `DESCRIBE books` showed that the actual primary key column defined in the database schema is `book_id`.

**Fix:**  
Updated all prepared SQL statements to select and bind on `book_id`, and updated query string links (`details.php?id=`) to use `$row['book_id']`.

**Verification:**  
Loaded `index.php`, `books.php`, and `gallery.php`. Verified that `mysqli_prepare()` succeeded without returning false, and detail page links correctly passed the numeric book ID.

---

# 🤖 Section 2: AI Usage Log

## AI Task 1

**Date:**  
28/09/2026

**Task Description:**  
Scaffold responsive layout, includes structure, and automatic database connection switching for localhost and Coreteaching environments.

**Tool Used:**  
Gemini / ChatGPT

**Prompt / Input:**  
"Provide includes for db_connect.inc, header.inc, nav.inc, and footer.inc for an RMIT PHP project that automatically detects localhost vs Jacob 5, uses Bootstrap 5, Righteous, and Elms Sans fonts."

**AI Output Summary:**  
Generated PHP include files with an environment check inspecting `$_SERVER['HTTP_HOST']`, Bootstrap 5 boilerplate, Google Fonts links, and semantic `<header>`, `<nav>`, `<main>`, and `<footer>` elements.

**What You Accepted:**  
Accepted the `db_connect.inc` environment detection conditional block and the Google Fonts link integration in `header.inc`.

**What You Changed:**  
Customized the student credentials, footer copyright text, and added the Material Icons link into the header since it was omitted in the initial suggestion.

**Validation Performed:**  
Tested locally on XAMPP and confirmed `db_connect.inc` resolved to `localhost` and connected to the `bookverse` database.

**Issues Identified:**  
The generic AI output contained placeholder database credentials that needed manual reconfiguration.

---

## AI Task 2

**Date:**  
30/09/2026

**Task Description:**  
Resolve broken folder routing causing Apache to serve PHP scripts as unparsed HTML text after accidental directory mixing.

**Tool Used:**  
Gemini / ChatGPT

**Prompt / Input:**  
"I mixed my old static assignment folder into my dynamic PHP folder in htdocs, and now my browser shows html code instead of running PHP. How do I fix the directory and Apache handling?"

**AI Output Summary:**  
Recommended auditing the directory structure against the assessment requirements, removing static `.html` files that took precedence in `DirectoryIndex`, and resetting the clean directory tree inside `a2/`.

**What You Accepted:**  
The clean file structure recommendation for `htdocs/wp-fork/a2/` containing only the required PHP files, `assets/`, and `includes/`.

**What You Changed:**  
Manually deleted leftover static files from Assignment 1 and restructured the local paths without wiping the existing Git repository history.

**Validation Performed:**  
Restarted Apache in XAMPP control panel and requested `http://localhost/wp-fork/a2/index.php`. Confirmed PHP executed server-side.

**Issues Identified:**  
None. The organizational advice directly solved the workspace corruption.

---

## AI Task 3

**Date:**  
01/10/2026

**Task Description:**  
Generate client-side status filtering and image preview in a single external JavaScript file without inline scripts.

**Tool Used:**  
Gemini / ChatGPT

**Prompt / Input:**  
"Write a pure JavaScript file scripts.js for books.php filtering using data-status attributes and an image upload preview for add.php with file extension checking for jpg, jpeg, png, gif, webp."

**AI Output Summary:**  
Provided DOMContentLoaded event listeners for `<select id="statusFilter">` to toggle table row displays via `row.style.display`, alongside a `FileReader` implementation to preview images before form upload.

**What You Accepted:**  
Accepted the `FileReader` image preview logic and the file extension whitelist validation array.

**What You Changed:**  
Refined the status filtering logic to handle case sensitivity by applying `.toLowerCase()` on both the selected value and the `data-status` attribute.

**Validation Performed:**  
Selected different statuses (Available, Reserved, Sold) in `books.php` to verify rows showed/hid dynamically without page reloads.

**Issues Identified:**  
Initial AI script assumed inline `onchange` attributes on the select tag, which violated the requirement prohibiting inline JavaScript. Rewrote it to use `addEventListener`.

---

## AI Task 4

**Date:**  
02/10/2026

**Task Description:**  
Diagnose and correct SQL prepared statement crashes (`Unknown column 'id'` and `Unknown column 'publish_year'`).

**Tool Used:**  
Gemini / ChatGPT

**Prompt / Input:**  
"Fatal error: Uncaught TypeError: mysqli_stmt_execute(): Argument #1 ($statement) must be of type mysqli_stmt, bool given in index.php and Unknown column 'id' in books.php. Here is my database table describe: book_id, title, author, genre, publication_year, isbn, description, book_condition, price, image_path, status, created_at."

**AI Output Summary:**  
Identified that `mysqli_prepare()` returned `false` because SQL queries referred to `id` and `publish_year` instead of `book_id` and `publication_year`. Provided refactored procedural MySQLi prepared statements.

**What You Accepted:**  
Accepted the corrected SQL queries and parameter binding strings matching the provided schema.

**What You Changed:**  
Integrated the updated queries across `index.php`, `books.php`, `gallery.php`, `details.php`, and `process_add.php`.

**Validation Performed:**  
Executed queries on localhost. Verified that all dynamic pages loaded records without database warnings or fatal errors.

**Issues Identified:**  
AI initially guessed column names like `year` and `id` before the schema was inspected via `DESCRIBE books`, causing temporary runtime crashes.

---

# 📌 Final Reflection (End of Assessment)

**What AI was most useful for:**  
AI was most helpful for rapidly bootstrapping standardized procedural MySQLi prepared statement templates, writing clean client-side JavaScript logic without inline event handlers, and diagnosing ambiguous fatal PHP type errors when statements failed to prepare.

**Where AI was incorrect or misleading:**  
AI frequently hallucinated database column names (such as assuming `id` instead of `book_id`, and `publish_year` or `year` instead of `publication_year`). It also generated code with non-standard syntax (such as an erroneous `endline;` tag), which caused syntax crashes until verified against the actual database schema.

**What you learned about debugging:**  
I learned that database-driven applications require strict alignment between the database schema and application code. When `mysqli_prepare()` fails, debugging the exact error via `mysqli_error($conn)` is far more effective than guessing column names. I also realized the importance of keeping workspaces clean to avoid configuration conflicts between static and dynamic assignments.

**How your approach changed over time:**  
Initially, I relied on AI output verbatim, which led to folder structure collisions and database schema mismatches. Over time, I shifted to using AI strictly as an advisory tool: inspecting database structures with `DESCRIBE` commands first, verifying code against W3C standards, and systematically testing every unit of code locally before committing.