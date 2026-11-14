# BEAL Consulting CMS coverage

Audit basis: the Laravel application routes, public Blade templates, current `config/beal.php` service data, database schema, media assets, and enquiry form were inspected before implementation. The CMS stores editable content in its own tables and uses the existing image led public design.

## Editable content inventory

| Website area | CMS location | Stored fields and behavior |
| --- | --- | --- |
| Home hero | Pages → Home → Hero | Eyebrow, location, headline and accent, rich text introduction, both CTA labels/URLs, note, caption, image/alt text, visibility |
| BEAL perspective | Pages → Home → The BEAL Perspective | Heading, rich text lead/body, image/alt/caption, principle repeater, commitment, visibility |
| Services overview | Pages → Home → Our Services | Heading, accent, rich text description, visibility; cards are managed under Services |
| Sector cards | Pages → Home → Sectors | Kicker, headings, rich text, image, repeated number/title/URL items, visibility |
| Insights | Pages → Home → Insights & Intelligence | Kicker, heading, rich text, link, image/alt/caption, publishing note, visibility |
| Experience | Pages → Home → Projects & Experience | Heading, rich text, CTA, image/alt/caption, visibility |
| Contact CTA | Pages → Home → Contact & Enquiries | Heading, rich text, button label, image, visibility; button opens the enquiry form |
| Service detail pages | Services | Name, slug, order number, short and formatted descriptions, image/alt, subservices, page copy, SEO, draft/published status |
| Header/navigation | Settings → Header | Announcement and positioning lines, CTA label/destination, editable navigation JSON |
| Footer | Settings → Footer | Statement, labels, navigation links, social link data |
| General/branding/SEO/integrations | Settings | Company information, logos and brand colors, SEO defaults/robots, verification and analytics IDs, enquiry recipient |
| Contact form | Enquiries | Validated, rate limited submissions are persisted; admins can mark status, search and export CSV |

## Security and publishing

- CMS access requires an active account with a named CMS role. Existing accounts receive the non CMS `none` role by default in the new migration.
- Create the first super administrator with `php artisan cms:user:create`; the command asks for credentials interactively and does not ship a default password.
- Homepage and service content edits save to draft fields. A CMS user with the relevant module permission can preview while signed in and publish explicitly. Draft preview pages send `noindex,nofollow`.
- Rich text is sanitized on the server. Unsupported active content, event handlers, unsafe URL schemes, and embedded scripts are removed.
- The media library includes built in files and uploads, with search, local preview/dimensions, drag-and-drop, actual byte transfer progress, cancel/retry, alternative text, copy URL, and protected deletion for referenced assets. Uploads are image-only, capped at 10 MB and 4000 × 4000 per image, and generate WebP thumbnails when GD supports WebP.
- Enquiries are stored even when outbound email is not configured. Email notification uses the saved CMS recipient or `BEAL_CONTACT_EMAIL` when a working mail transport is configured.
- Important edits and account, media, service, setting, and submission actions are written to the CMS activity log. Page/service revision snapshots are stored.

## Implemented admin areas

Dashboard metrics and recent activity; page and section editing; homepage and service drafts, previews and publishing; service CRUD; image uploads and alt text; global settings; enquiry search/detail/status/CSV export; user creation and role/status management; activity list; CMS search.

## Coverage limits

- This website currently has one designed public homepage and service detail pages. No project portfolio, team directory, testimonials, news feed, downloadable resource library, redirects manager, or CAPTCHA integration existed in the audited site, so those modules were not invented.
- Repeatable sector/principle data and structured navigation/footer settings are editable as JSON. The CMS does not yet provide a drag-and-drop repeater or visual header/footer builder.
- Media uploads show aggregate request progress for a multi-image batch. Resumable/chunked uploads and individual retry/cancel for each file are not implemented.
- Homepage section order is stored, but the fixed editorial Blade composition does not expose a reorder control; the current public layout retains its authored sequence.
- Settings save directly because they are global operational values; draft/preview publishing applies to homepage sections and service content.
- Service detail scope/CTA copy is editable as structured JSON; it is not a second visual rich text canvas.
- Media thumbnails are generated, but responsive `srcset` variants and EXIF orientation correction are not implemented.
- The integration settings store IDs/configuration used by the site; analytics/GTM scripts and third party service verification still need production-specific configuration and verification.
- Enquiry records are retained without an automated retention policy. Set one to match the organization’s operational and legal requirements.
- Account recovery/password reset emails are not configured. Super administrators can deactivate accounts and create replacements; use a secure out-of-band channel to deliver initial credentials.

## Verification performed

- CMS feature suite covers login boundaries, module permissions, sanitized homepage drafts and publishing, service drafts and publishing, media uploads/metadata, settings persistence, and enquiry storage.
- `php artisan test --compact`
- `npm run build`
- `php artisan route:list --except-vendor`
- `php artisan view:cache`
