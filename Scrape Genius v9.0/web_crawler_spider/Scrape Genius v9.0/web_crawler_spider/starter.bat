@echo off

REM Step 1: Change directory to the folder where the .bat file is located
cd /d "%~dp0"


REM Step 3: Optional - open this folder in File Explorer
start "" explorer "%cd%"

REM Step 4: Run npm start in current folder
start cmd /k "npm start"
