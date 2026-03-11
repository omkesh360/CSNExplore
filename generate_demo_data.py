import json
import random

# Real looking seed data
S_IMG = ["https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1542314831-c6a4d27ce6a2?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1517840901100-8179e982acb7?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1551882547-ff40c0d12c56?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1611892440504-42a792e24d32?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?ixlib=rb-4.0.3&w=800&q=80"]
C_IMG = ["https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1550355291-bbee04a92027?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1552519507-da3b142c6e3d?ixlib=rb-4.0.3&w=800&q=80"]
B_IMG = ["https://images.unsplash.com/photo-1558981403-c5f9899a28bc?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1627834575836-39149bb88ed1?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1568772585407-9361f9bf3a87?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1449426468159-d96dbf08f19f?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1591104107102-1acc8d6b1390?ixlib=rb-4.0.3&w=800&q=80"]
R_IMG = ["https://images.unsplash.com/photo-1552566626-52f8b828add9?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1544148103-0773bf10d330?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1514933651103-005eec06c04b?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&w=800&q=80"]
A_IMG = ["https://images.unsplash.com/photo-1564507592224-2fc8c61bb2b1?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1627891398124-7bd08a462db9?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1534152011707-1c667634f509?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1587595431973-160d0d94add1?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1506461883276-594a12b11cf3?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1516483638261-f4dafaf00bc7?ixlib=rb-4.0.3&w=800&q=80"]
BUS_IMG = ["/images/purple-travels-bus.jpg", "https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1570125909232-eb263c188f7e?ixlib=rb-4.0.3&w=800&q=80", "https://images.unsplash.com/photo-1464219222984-216ebbfdba34?ixlib=rb-4.0.3&w=800&q=80"]

badges = ["", "", "Bestseller", "Premium", "Popular", "Great Value", "Top Choice", "Must Try"]

def get_badge():
    b = random.choice(badges)
    return b if b else None

cities = ["Mumbai", "Pune", "Delhi", "Goa", "Nashik", "Bangalore", "Jaipur", "Kochi", "Manali", "Shimla", "Aurangabad", "Nagpur", "Surat", "Ahmedabad", "Udaipur", "Agra", "Varanasi"]
desc_hotel = ["Luxury 5-star property with stunning views.", "Cozy boutique hotel in the city center.", "Affordable stay with complimentary breakfast.", "Resort with private pool and spa.", "Business hotel perfect for short trips.", "Eco-friendly retreat surrounded by nature.", "Heritage property with royal architecture.", "Sea-facing rooms with private balconies.", "Backpacker hostel with vibrant community.", "Modern apartments with full kitchen."]
desc_bike = ["Perfect for city commute.", "Great for long highway rides with pillion comfort.", "Aggressive styling and peppy engine.", "Reliable companion for rough terrains.", "Smooth riding with excellent mileage."]
desc_rest = ["Award-winning chef's special menu.", "Rooftop dining with live music.", "Authentic flavors passed down generations.", "Quirky interiors and great coffee.", "Perfect for romantic dinners.", "Quick bites and casual hangouts.", "Fine dining experience by the lake."]
desc_attr = ["A must-visit historical monument.", "Breathtaking natural landscapes.", "Fun activities for the whole family.", "Guided tours available daily.", "Experience the local culture and heritage.", "Perfect spot for photography."]

demo_data = {
    'stays': [], 'cars': [], 'bikes': [], 'restaurants': [], 'attractions': [], 'buses': []
}

# Generate 20 Stays
hotel_prefixes = ["The Grand", "Royal", "Sunset", "Mountain", "City Center", "Lakeview", "Golden", "Majestic", "Palm", "Oasis", "Heritage", "Sapphire", "Emerald", "Crystal", "Paradise", "Serenity", "Tranquil", "Harmony", "Zen", "Urban"]
hotel_suffixes = ["Palace", "Resort", "Inn", "Hotel", "Retreat", "Lodge", "Suites", "Villas", "Boutique", "Haven"]
for i in range(1, 21):
    n = f"{random.choice(hotel_prefixes)} {random.choice(hotel_suffixes)}"
    demo_data['stays'].append({
        "id": i, "name": n, "location": random.choice(cities),
        "description": random.choice(desc_hotel),
        "price": random.randint(15, 120) * 100,
        "rating": round(random.uniform(6.5, 9.8), 1),
        "reviews": random.randint(10, 1500),
        "badge": get_badge(), "image": random.choice(S_IMG)
    })

# Generate 20 Cars
car_models = [("Hyundai Creta", "SUV"), ("Maruti Swift", "Hatchback"), ("Honda City", "Sedan"), ("Mahindra Thar", "Offroad"), ("Toyota Innova", "MUV"), ("Tata Nexon", "SUV"), ("Kia Seltos", "SUV"), ("Maruti Baleno", "Hatchback"), ("Hyundai Verna", "Sedan"), ("Toyota Fortuner", "SUV"), ("Mahindra XUV700", "SUV"), ("Maruti Ertiga", "MUV"), ("Honda Amaze", "Sedan"), ("Tata Tiago", "Hatchback")]
providers = ["Zoomcar", "Revv", "Avis", "Savaari", "Local Rents", "Drivezy"]
for i in range(1, 21):
    model, ctype = random.choice(car_models)
    demo_data['cars'].append({
        "id": i, "name": model, "provider": random.choice(providers), "location": random.choice(cities),
        "dailyRate": random.randint(10, 45) * 100, "passengers": random.choice([4, 5, 7]),
        "transmission": random.choice(["Automatic", "Manual"]), "type": ctype,
        "rating": round(random.uniform(7.0, 9.6), 1), "reviews": random.randint(15, 800),
        "badge": get_badge(), "image": random.choice(C_IMG)
    })

# Generate 20 Bikes
bike_models = [("Royal Enfield Classic 350", "Cruiser"), ("Honda Activa 6G", "Scooter"), ("KTM Duke 200", "Sports"), ("Bajaj Avenger 220", "Cruiser"), ("TVS Jupiter", "Scooter"), ("Yamaha R15", "Sports"), ("Suzuki Access 125", "Scooter"), ("Royal Enfield Himalayan", "Adventure"), ("Bajaj Pulsar NS200", "Sports"), ("Hero Splendor", "Commuter"), ("Ather 450X", "Electric"), ("Ola S1 Pro", "Electric")]
for i in range(1, 21):
    model, btype = random.choice(bike_models)
    demo_data['bikes'].append({
        "id": i, "name": model, "type": btype, "description": random.choice(desc_bike),
        "features": random.choice(["2 Helmets Included", "Helmet, First Aid", "Full Gear Available", "Phone Mount included", "Riding Jacket included"]),
        "dailyRate": random.randint(3, 15) * 100, "rating": round(random.uniform(7.5, 9.5), 1),
        "reviews": random.randint(20, 900), "badge": get_badge(), "image": random.choice(B_IMG)
    })

# Generate 20 Restaurants
rest_names = ["Spice Route", "Ocean Grill", "Cafe Mocha", "The Steakhouse", "Punjabi Dhaba", "Sushi Train", "Tuscany Villa", "Dragon Wok", "Pind Balluchi", "Barbeque Nation", "Hard Rock Cafe", "Mainland China", "Sigree Global Grill", "The Yellow Chilli", "Olive Bar & Kitchen", "Indigo Delicatessen", "Social", "Farzi Cafe", "Peshawri", "Bukhara"]
cuisines = ["North Indian", "South Indian", "Chinese", "Italian", "Continental", "Seafood", "Japanese", "Cafe & Desserts", "Pan Asian", "Mughlai", "Street Food", "Vegan/Healthy"]
for i in range(1, 21):
    n = rest_names[i-1]
    demo_data['restaurants'].append({
        "id": i, "name": n, "cuisine": random.choice(cuisines), "location": random.choice(cities),
        "description": random.choice(desc_rest), "pricePerPerson": random.randint(3, 30) * 100,
        "rating": round(random.uniform(7.0, 9.7), 1), "reviews": random.randint(50, 2500),
        "badge": get_badge(), "image": random.choice(R_IMG)
    })

# Generate 20 Attractions
attr_names = ["Taj Mahal Guided Tour", "Elephanta Caves Trip", "Desert Safari", "Amber Fort Visit", "Kerala Houseboat", "City Museum", "Botanical Gardens", "Historical Palace Tour", "Wildlife Safari", "River Rafting Adventure", "Mountain Trekking", "Scuba Diving Exp", "Hot Air Balloon Ride", "Cultural Heritage Walk", "Vineyard Tasting Tour", "Amusement Park Day Pass", "Snow Theme Park", "Planetarium Show", "Aquarium Visit", "Sunset Cruise"]
for i in range(1, 21):
    n = attr_names[i-1]
    efe = random.choice([0, 0, 100, 200, 500, 800, 1500, 2500, 4000, 6000])
    demo_data['attractions'].append({
        "id": i, "name": n, "location": random.choice(cities),
        "duration": random.choice(["2h 00m", "3h 30m", "Half Day", "Full Day", "2 Days", "4h 00m", "1h 30m"]),
        "description": random.choice(desc_attr), "entryFee": efe,
        "rating": round(random.uniform(7.8, 9.9), 1), "reviews": random.randint(100, 5000),
        "badge": get_badge(), "image": random.choice(A_IMG)
    })

# Generate 20 Buses
bus_names = ["Purple Travels", "Neeta Tours", "VRL Travels", "Konduskar Travels", "Prasanna Purple", "MSRTC Shivneri", "SRS Travels", "Orange Tours", "KSRTC Airavat", "IntrCity SmartBus", "Zingbus", "NueGo", "Chartered Bus", "Hans Travels", "Pooja Travels", "Mahasagar Travels", "Khurana Travels", "Sanjay Travels", "Saini Travels", "Rishabh Travels"]
bus_types = ["Volvo AC", "Sleeper Non-AC", "Volvo Multi-Axle", "AC Sleeper", "Seater Non-AC", "AC Seater", "BharatBenz AC Sleeper", "Scania AC Multi Axle Semi Sleeper"]
for i in range(1, 21):
    n = bus_names[i-1]
    c1 = random.choice(cities)
    c2 = random.choice([c for c in cities if c != c1])
    demo_data['buses'].append({
        "id": i, "name": n + (" " + random.choice(["Volvo", "Sleeper", "Express"]) if random.random() > 0.5 else ""),
        "route": f"{c1} → {c2}",
        "departure": f"{random.randint(1,12):02d}:{random.choice(['00','15','30','45'])} {random.choice(['AM','PM'])}",
        "duration": f"{random.randint(2,16)}h {random.choice(['00','15','30','45'])}m",
        "type": random.choice(bus_types), "price": random.randint(25, 150) * 10,
        "rating": round(random.uniform(6.0, 9.5), 1), "reviews": random.randint(20, 1200),
        "badge": get_badge(), "image": random.choice(BUS_IMG)
    })

# Convert structure to JS code
js_code = f"    function getDemoData(category) {{\n        const data = {json.dumps(demo_data, indent=12)};\n        return data[category] || [];\n    }}"

with open("public/js/listings.js", "r") as f:
    text = f.read()

import re
text = re.sub(r'    function getDemoData\(category\) \{.*?(?=    // ={60}\n    // FILTER \+ SORT LOGIC)', js_code + "\n\n", text, flags=re.DOTALL)

with open("public/js/listings.js", "w") as f:
    f.write(text)

print("Successfully injected 120 massive demo items into listings.js")
