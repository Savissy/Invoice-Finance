# AGENTS.md

## Purpose
This repository powers a public-facing website/web app. Reviews must prioritize user safety, browser trust, anti-phishing safeguards, and common web security issues.

## Review priorities
When reviewing changes, prioritize these categories before style or refactoring comments:

1. Phishing / deceptive UX
2. Malware-like or browser-warning-triggering behavior
3. Credential theft / wallet theft patterns
4. Public web security vulnerabilities
5. Dangerous external dependencies / remote resources
6. Session/auth mistakes
7. Upload / redirect / injection risks

## Treat the following as HIGH PRIORITY
Flag these aggressively:

- Any UI that asks for:
  - seed phrases
  - recovery phrases
  - private keys
  - wallet secrets
  - raw passwords in unusual contexts
- Any page that imitates a known wallet, exchange, bank, payment gateway, browser warning, OS dialog, or security prompt
- Hidden iframes, hidden forms, invisible overlays, or clickjacking patterns
- JavaScript redirects, meta refresh redirects, chained redirects, or unvalidated redirect targets
- Obfuscated JavaScript or intentionally concealed payloads
- `eval`, `new Function`, unsafe `innerHTML`, `document.write`, decoded payload execution
- Forms posting sensitive data to external domains
- Automatic downloads or popups without clear user intent
- Third-party scripts loaded from unknown or mismatched domains
- Any use of HTTP assets on an HTTPS site
- Upload handlers that do not strongly validate file type, extension, size, and storage path
- Reflected or stored XSS
- SQL injection
- Command injection
- Path traversal / local file inclusion
- Missing authorization checks on admin pages or admin APIs
- Secrets committed to the repo

## Files to scrutinize first
Review these areas first whenever they change:

- `login*`
- `register*`
- `logout*`
- `admin/*`
- `dashboard*`
- `wallet*`
- `connect*`
- `checkout*`
- `payment*`
- `upload*`
- `contact*`
- `config*`
- `.htaccess`
- JavaScript files that manipulate DOM, redirects, forms, storage, cookies, or network requests

## Browser-trust specific checks
Explicitly check for things that may cause browsers or reputation systems to distrust the site:

- deceptive copy or brand impersonation
- suspicious redirects
- mixed content
- unsafe downloads
- hidden data collection
- unusual credential prompts
- compromised or suspicious third-party assets
- pages that appear to socially engineer users

## For PHP reviews
Always check for:
- prepared statements for database access
- output escaping
- CSRF protection on state-changing forms
- strict file upload validation
- session fixation / insecure cookie handling
- access control on admin pages
- direct object reference issues
- insecure includes / path usage

## For JavaScript reviews
Always check for:
- dangerous sinks (`innerHTML`, `outerHTML`, `insertAdjacentHTML`, `eval`, `Function`)
- redirect logic
- dynamic script injection
- localStorage/sessionStorage use for secrets
- credential or wallet data capture
- suspicious event handlers or hidden overlays

## Review output format
For every meaningful issue, report:
- severity
- file path
- exact risky pattern
- why it matters
- concrete fix

If something looks suspicious but may be legitimate, place it under:
- “Needs manual review”

If a pattern is safe and intentionally used, place it under:
- “Looks acceptable”

## Severity guidance
Use:
- Critical: likely theft, phishing, malware-like behavior, remote code execution, auth bypass
- High: exploitable injection, unsafe uploads, admin exposure, serious redirect abuse
- Medium: mixed content, weak validation, suspicious third-party assets, missing hardening
- Low: defense-in-depth, cleanup, safer defaults

## Do not down-rank these
Treat deceptive wallet flows, seed phrase requests, hidden frames/forms, obfuscated scripts, and suspicious redirects as severe even if they are not currently exploitable in a traditional sense.

## Final checklist in every substantial review
At the end of the review, include:
- browser warning risk summary
- phishing risk summary
- compromised-site risk summary
- deployment hardening checklist
