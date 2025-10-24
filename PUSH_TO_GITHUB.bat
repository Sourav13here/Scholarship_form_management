@echo off
title Push to GitHub - Nucleon Scholarship System
color 0A

echo ========================================================
echo   PUSH TO GITHUB - NUCLEON SCHOLARSHIP SYSTEM
echo ========================================================
echo.
echo This script will help you push your project to GitHub
echo.
echo PREREQUISITES:
echo   1. Git must be installed
echo   2. GitHub account created
echo   3. Repository created on GitHub
echo.
pause

echo.
echo ========================================================
echo   STEP 1: Initializing Git Repository
echo ========================================================
echo.
git init
if %errorlevel% neq 0 (
    echo ERROR: Git initialization failed!
    echo Please install Git from: https://git-scm.com/
    pause
    exit /b 1
)
echo SUCCESS: Git repository initialized
echo.
pause

echo.
echo ========================================================
echo   STEP 2: Adding All Files
echo ========================================================
echo.
git add .
if %errorlevel% neq 0 (
    echo ERROR: Failed to add files!
    pause
    exit /b 1
)
echo SUCCESS: All files added
echo.
pause

echo.
echo ========================================================
echo   STEP 3: Creating Initial Commit
echo ========================================================
echo.
git commit -m "Initial commit: Nucleon Scholarship Application System"
if %errorlevel% neq 0 (
    echo ERROR: Commit failed!
    pause
    exit /b 1
)
echo SUCCESS: Initial commit created
echo.
pause

echo.
echo ========================================================
echo   STEP 4: Add Remote Repository
echo ========================================================
echo.
echo Please enter your GitHub repository URL
echo Example: https://github.com/username/nucleon-scholarship-system.git
echo.
set /p REPO_URL="Repository URL: "

git remote add origin %REPO_URL%
if %errorlevel% neq 0 (
    echo.
    echo NOTE: Remote 'origin' might already exist
    echo Removing existing remote and adding new one...
    git remote remove origin
    git remote add origin %REPO_URL%
)
echo SUCCESS: Remote repository added
echo.
pause

echo.
echo ========================================================
echo   STEP 5: Renaming Branch to 'main'
echo ========================================================
echo.
git branch -M main
echo SUCCESS: Branch renamed to 'main'
echo.
pause

echo.
echo ========================================================
echo   STEP 6: Pushing to GitHub
echo ========================================================
echo.
echo You will be asked for your GitHub credentials:
echo   Username: Your GitHub username
echo   Password: Use Personal Access Token (NOT your password)
echo.
echo To create a Personal Access Token:
echo   1. Go to GitHub Settings
echo   2. Developer settings
echo   3. Personal access tokens
echo   4. Generate new token
echo   5. Select 'repo' scope
echo   6. Copy and use as password
echo.
pause

git push -u origin main
if %errorlevel% neq 0 (
    echo.
    echo ERROR: Push failed!
    echo.
    echo Common issues:
    echo   1. Wrong credentials - Use Personal Access Token
    echo   2. Repository doesn't exist - Create it on GitHub first
    echo   3. Network issues - Check internet connection
    echo.
    pause
    exit /b 1
)

echo.
echo ========================================================
echo   SUCCESS! PROJECT PUSHED TO GITHUB
echo ========================================================
echo.
echo Your project is now on GitHub!
echo.
echo Next steps:
echo   1. Visit your repository on GitHub
echo   2. Verify all files are uploaded
echo   3. Add a description and topics
echo   4. Share the repository link
echo.
echo Repository URL: %REPO_URL%
echo.
pause
