# How to Push PipPip API to GitHub

## Current Situation
- ✅ All code is committed locally
- ✅ Remote repository configured: https://github.com/envision-tech-co/piepie-api.git
- ❌ Need authentication to push

## GitHub Authentication (Required)

**Important:** GitHub no longer accepts password authentication. You must use a Personal Access Token (PAT).

### Step 1: Create a Personal Access Token

1. **Go to GitHub and login with:**
   - Email: hello@envision-tech.co.in
   - Password: [Your GitHub password]

2. **Navigate to:**
   - Click your profile picture (top right)
   - Settings → Developer settings → Personal access tokens → Tokens (classic)
   - Or direct link: https://github.com/settings/tokens

3. **Generate new token:**
   - Click "Generate new token (classic)"
   - Note: "PipPip API Push Access"
   - Expiration: 90 days (or your preference)
   - Select scopes:
     - ✅ `repo` (Full control of private repositories)
   - Click "Generate token" at the bottom

4. **Copy the token immediately!**
   - It looks like: `ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`
   - You won't be able to see it again
   - Save it somewhere safe temporarily

### Step 2: Push Using the Token

Open your terminal in the project directory and run:

```bash
git push -u origin main
```

When prompted:
- **Username:** `hello@envision-tech.co.in` OR your GitHub username
- **Password:** Paste the Personal Access Token (NOT your GitHub password)

### Alternative: Push with Token in URL (One-time)

```bash
git remote set-url origin https://YOUR_TOKEN@github.com/envision-tech-co/piepie-api.git
git push -u origin main
```

Replace `YOUR_TOKEN` with the actual token you generated.

**⚠️ Warning:** This stores the token in plain text in `.git/config`. Remove it after pushing:

```bash
git remote set-url origin https://github.com/envision-tech-co/piepie-api.git
```

## Quick Commands (Copy & Paste)

### If you have the token ready:

```bash
# Set remote with token (replace YOUR_TOKEN_HERE)
git remote set-url origin https://YOUR_TOKEN_HERE@github.com/envision-tech-co/piepie-api.git

# Push
git push -u origin main

# Remove token from config (security)
git remote set-url origin https://github.com/envision-tech-co/piepie-api.git
```

### Or use credential helper:

```bash
# Push (will prompt for credentials)
git push -u origin main

# When prompted:
# Username: hello@envision-tech.co.in
# Password: [paste your Personal Access Token]
```

## Verify Push Success

After successful push, verify at:
https://github.com/envision-tech-co/piepie-api

You should see:
- ✅ All files including .env
- ✅ 91 files committed
- ✅ AUTH_DOCUMENTATION.md
- ✅ postman_collection.json
- ✅ Complete Laravel application

## What's Being Pushed

### Complete Authentication System
- Customer auth (Phone + OTP)
- Provider auth (Phone + OTP)  
- Admin auth (Email + Password)
- All migrations and seeders
- Admin dashboard views
- API documentation
- Postman collection

### Important Files Included
- ✅ `.env` (development configuration)
- ✅ `composer.lock` (dependency versions)
- ✅ All migrations
- ✅ All models, controllers, middleware
- ✅ Admin login views
- ✅ Complete documentation

## Troubleshooting

### Error: "Permission denied"
- Make sure you're logged into the correct GitHub account
- Verify you have write access to `envision-tech-co/piepie-api`
- Check if the repository exists

### Error: "Authentication failed"
- You're using your password instead of the token
- Generate a new Personal Access Token
- Make sure the token has `repo` scope

### Error: "Repository not found"
- Check if the repository exists: https://github.com/envision-tech-co/piepie-api
- Verify you have access to the `envision-tech-co` organization
- You may need to create the repository first

### Need to Create Repository First?

If the repository doesn't exist yet:

1. Go to: https://github.com/organizations/envision-tech-co/repositories/new
2. Repository name: `piepie-api`
3. Description: "PipPip Backend API - On-demand automotive services platform"
4. Private repository (recommended)
5. Don't initialize with README (we already have one)
6. Click "Create repository"
7. Then run the push command

## After Successful Push

1. **Verify on GitHub:**
   - Check all files are present
   - Review the commit message
   - Ensure .env is there

2. **Share with team:**
   - Repository URL: https://github.com/envision-tech-co/piepie-api
   - Clone command: `git clone https://github.com/envision-tech-co/piepie-api.git`

3. **Security Note:**
   - The .env file contains development credentials
   - Change APP_KEY and admin password on production
   - See DEPLOYMENT_INSTRUCTIONS.md for details

## Need Help?

If you encounter any issues:
1. Check if repository exists on GitHub
2. Verify your GitHub account has access
3. Ensure Personal Access Token has correct permissions
4. Try creating the repository first if it doesn't exist

---

**Ready to push!** Just need your Personal Access Token from GitHub.
