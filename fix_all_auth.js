const fs = require('fs');
const bcrypt = require('bcryptjs');

const hash = bcrypt.hashSync('admin123', 10);
const usersFile = 'data/users.json';
const users = JSON.parse(fs.readFileSync(usersFile, 'utf8'));
let admin = users.find(u => u.email === 'admin@travelhub.com');
if (admin) {
    admin.password = hash;
} else {
    admin = {
        id: 1,
        name: "Admin User",
        email: "admin@travelhub.com",
        password: hash,
        role: "Admin",
        status: "Active"
    };
    users.unshift(admin);
}
fs.writeFileSync(usersFile, JSON.stringify(users, null, 2));
console.log('Fixed users.json with universal hash: ' + hash);
