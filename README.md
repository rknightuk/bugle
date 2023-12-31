## Bugle

![Screenshots of Bugle](public/assets/screenshot_small.png)

**What does it do?**

- Create ActivityPub profiles
- Mastodon profile links
- Avatar and header images
- Send toots (with Markdown!)
- See replies, boosts, and likes
- Attach photos to toots
- Reply or @mention people in toots
- Edit toots

**What doesn't it do?**

- Use this with your mastodon client
- Show featured posts on profile
- Allow you to follow accounts

**Can I use it for my ActivityPub needs?**

You can but you probably shouldn't. I built it as a way to learn how ActivityPub works so although I am using it, I probably wouldn't recommend it. You'd almost definitely be better off with a full Mastodon instance. The installation instructions are here if you really want to use it though:

## Installation

1. Clone the repository
2. Copy the example `.env.example` file. `cp .env.example .env`
3. Update `DOMAIN` with your domain, database credentials, and S3 credentials (for images)
4. `composer install`
5. `php artisan migrate`
6. `php artisan app:create-user` to create a user login
7. `npm i`
8. `npm run dev`
9. Go to `/login` to login and add manage profiles.
