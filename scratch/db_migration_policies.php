<?php
require_once __DIR__ . '/../php/config.php';

try {
    $db = getDB();
    $categories = ['stays', 'cars', 'bikes', 'restaurants', 'attractions', 'buses'];

    foreach ($categories as $cat) {
        echo "Updating table: $cat...\n";
        
        // Add policies column if not exists
        try {
            $db->query("ALTER TABLE $cat ADD COLUMN policies TEXT DEFAULT NULL");
            echo "  Added policies column.\n";
        } catch (Exception $e) {
            echo "  policies column already exists or error: " . $e->getMessage() . "\n";
        }

        // Initialize with standard policies if empty
        $standardPolicies = [];
        if ($cat === 'cars' || $cat === 'bikes') {
            $standardPolicies = [
                'requirements' => [
                    ['icon' => 'id_card', 'text' => 'Valid Indian Driving Licence (LMV / MCWG as applicable) is mandatory for all renters.'],
                    ['icon' => 'public', 'text' => 'Foreign nationals must carry a valid International Driving Permit (IDP) along with their home-country licence.'],
                    ['icon' => 'badge', 'text' => 'Original licence must be presented at vehicle handover. Digital / photocopy not accepted.']
                ],
                'terms' => [
                    ['icon' => 'local_gas_station', 'text' => 'Fuel is not included. Vehicle must be returned with the same fuel level as at pick-up.'],
                    ['icon' => 'payments', 'text' => 'A refundable security deposit may be required at the time of vehicle handover.'],
                    ['icon' => 'no_smoking', 'text' => 'Smoking and consumption of alcohol inside the vehicle is strictly prohibited.']
                ]
            ];
        } else if ($cat === 'stays') {
            $standardPolicies = [
                'requirements' => [
                    ['icon' => 'id_card', 'text' => 'Original Identity Proof (Aadhar Card, Passport, or Voter ID) is mandatory for all guests.'],
                    ['icon' => 'location_off', 'text' => 'Local ID proof might not be accepted as per hotel policy.'],
                    ['icon' => 'groups', 'text' => 'All guests must be registered at the time of check-in.']
                ],
                'terms' => [
                    ['icon' => 'schedule', 'text' => 'Standard Check-in: 12:00 PM | Standard Check-out: 10:00 AM.'],
                    ['icon' => 'payments', 'text' => 'A refundable security deposit may be required at the time of check-in.'],
                    ['icon' => 'no_smoking', 'text' => 'Smoking and consumption of alcohol inside rooms is subject to hotel rules.']
                ]
            ];
        } else {
            $standardPolicies = [
                'requirements' => [
                    ['icon' => 'id_card', 'text' => 'Original Identity Proof is mandatory for entry/booking.'],
                ],
                'terms' => [
                    ['icon' => 'info', 'text' => 'Please follow the site-specific rules and regulations.'],
                    ['icon' => 'no_smoking', 'text' => 'Smoking and consumption of alcohol is subject to local regulations.']
                ]
            ];
        }

        $jsonPolicies = json_encode($standardPolicies);
        $db->query("UPDATE $cat SET policies = ? WHERE policies IS NULL OR policies = ''", [$jsonPolicies]);
        echo "  Initialized policies for $cat.\n";
    }

    echo "Migration complete!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
