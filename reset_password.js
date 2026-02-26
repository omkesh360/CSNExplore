const fs = require('fs');
const path = require('path');
const bcrypt = require('bcryptjs');

const usersFile = path.join(__dirname, 'data', 'users.json');
const newPassword = 'admin123';

console.log('Reading users file...');
const users = JSON.parse(fs.readFileSync(usersFile, 'utf8'));

const adminUser = users.find(u => u.role === 'Admin');

if (adminUser) {
    console.log(`Found Admin user: ${adminUser.email}`);
    const salt = bcrypt.genSaltSync(10);
    const hash = bcrypt.hashSync(newPassword, salt);
    adminUser.password = hash;

    fs.writeFileSync(usersFile, JSON.stringify(users, null, 4));
    console.log('Password updated successfully for Admin user.');
} else {
    console.error('Admin user not found!');
}
