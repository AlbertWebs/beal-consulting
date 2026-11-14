# BEAL Consulting CMS operations

## One time setup

1. Configure the application URL, database and session settings in the deployment environment.
2. Configure Laravel mail delivery and set `BEAL_CONTACT_EMAIL` as a fallback recipient. The CMS recipient is editable under Settings → Integrations.
3. Install dependencies and apply additive migrations:

   ```sh
   composer install --no-interaction --prefer-dist --optimize-autoloader
   npm ci
   php artisan migrate --no-interaction
   php artisan db:seed --class=WebsiteContentSeeder --no-interaction
   php artisan storage:link --no-interaction
   npm run build
   ```

   `WebsiteContentSeeder` is idempotent and only inserts missing website content. It does not overwrite later editorial changes.

4. Create the initial super administrator with `php artisan cms:user:create`. This command prompts for a name, email and password; use a password of at least 12 characters. There are no built in CMS credentials.
5. Sign in at `/admin/login`.

Do not run `migrate:fresh` against an environment containing data. Back up the database and uploaded files before production migrations.

## Publishing homepage changes

Open Pages → Home. Editing a section autosaves the draft after a short pause. Drafts stay out of the public homepage until published. Use **Preview draft** while signed in to inspect the assembled page, then **Publish changes** when ready. A page status of Draft or Unpublished removes it from the public route; an authorized preview remains available.

The rich text editor supports paragraphs, H2–H6, emphasis, lists, links, blockquotes, alignment, tables, undo/redo, and images. Rich text is sanitized on save. Use only local media URLs for inserted images. Repeating sector and principle items are edited in JSON; keep their documented keys and valid JSON syntax.

## Publishing services

Services are edited from Services. Changes save to a draft, and authenticated users with service permission can preview that draft. **Publish service** copies the saved draft to the public service record. New services start as drafts. Changing a published service to Draft or Unpublished immediately removes it from the public service list.

## Media

Upload images from Media library. Accepted formats are JPEG, PNG, WebP and GIF, up to 10 MB and 4000 × 4000 pixels per file, with up to 20 images in one request. Drag files into the drop zone or choose them; previews show local dimensions before upload. The progress indicator uses actual browser upload byte events, and the active request can be cancelled or retried after an error. Add meaningful alternative text and use Copy URL for image insertion. Existing site and logo files are protected. An image referenced by a page, service, setting or Open Graph field cannot be deleted until its references are removed.

## Enquiries and users

Enquiries are stored in the CMS even if email is offline. Configure mail transport to receive notification email; submissions remain accessible under Enquiries. Use statuses to track handling and CSV export for local processing. Treat exported files as private data.

Only super administrators can manage CMS users. Administrators can manage settings, enquiries, and activity. Editors and content managers can manage pages, services, and media. Deactivating an account blocks CMS access. The UI prevents self-deactivation and deleting the last super administrator.

## Routine checks

- Review Dashboard content health for pages missing SEO descriptions and images missing alternative text.
- Review Activity log after significant changes.
- Keep database backups and `storage/app/public` backups together.
- After deployment, run `php artisan migrate --no-interaction`, `php artisan storage:link --no-interaction` where required, and deploy the Vite build artifact.
- Run the feature suite with `php artisan test --compact` before release.
