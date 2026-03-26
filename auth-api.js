const express = require('express');
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const nodemailer = require('nodemailer');

const app = express();
app.use(express.json());

const users = {}; // Simple in-memory user store, replace with your database

// Signup API
app.post('/signup', async (req, res) => {
    const { email, phone, password } = req.body;
    const hashedPassword = await bcrypt.hash(password, 10);
    users[email] = { email, phone, password: hashedPassword, verified: false }; // Store user
    // Send verification email/notification here
    res.status(201).send('User created, verification pending.');
});

// Login API
app.post('/login', async (req, res) => {
    const { email, password } = req.body;
    const user = users[email];
    if (user && await bcrypt.compare(password, user.password)) {
        const token = jwt.sign({ email: user.email }, 'secret-key');
        res.json({ token });
    } else {
        res.status(401).send('Invalid credentials');
    }
});

// Password Reset API
app.post('/reset-password', async (req, res) => {
    const { email } = req.body;
    const tempPassword = Math.random().toString(36).slice(-8); // Generate temp password
    const hashedTempPassword = await bcrypt.hash(tempPassword, 10);
    if (users[email]) {
        users[email].password = hashedTempPassword; // Update password
        // Send temp password via email or SMS
        // nodemailer setup; code to send email goes here
        res.send('Temporary password sent.');
    } else {
        res.status(404).send('User not found');
    }
});

// Verification API
app.post('/verify', (req, res) => {
    const { email } = req.body;
    if (users[email]) {
        users[email].verified = true; // Update user verification
        res.send('User verified.');
    } else {
        res.status(404).send('User not found');
    }
});

// Password Change Notification
function notifyPasswordChange(email) {
    // Notify the user about the password change via email or SMS
}

// Start the server
const PORT = 3000;
app.listen(PORT, () => console.log(`Server running on port ${PORT}`));