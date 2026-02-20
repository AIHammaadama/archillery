<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Material Categories
    |--------------------------------------------------------------------------
    |
    | Predefined categories for materials used in procurement.
    | These categories help organize and classify materials for better management.
    |
    */
    'categories' => [
        'Asphalt' => 'Asphalt',
        'Blocks' => 'Blocks',
        'CCTV & Telecoms' => 'CCTV & Telecoms',
        'Cement & Additives' => 'Cement & Additives',
        'Damp-Proof Membrane' => 'Damp-Proof Membrane',
        'Doors' => 'Doors',
        'Electrical Appliances' => 'Electrical Appliances',
        'Electrical Materials & Fittings' => 'Electrical Materials & Fittings',
        'Fasteners & Fixings' => 'Fasteners & Fixings',
        'Finishes & Interior Materials' => 'Finishes & Interior Materials',
        'Glass & Aluminium' => 'Glass & Aluminium',
        'Hardscape' => 'Hardscape',
        'HVAC' => 'HVAC',
        'Kitchen Appliances' => 'Kitchen Appliances',
        'Labour' => 'Labour',
        'Miscellaneous' => 'Miscellaneous',
        'Office & IT Supplies' => 'Office & IT Supplies',
        'Other Flooring Materials' => 'Other Flooring Materials',
        'Paints' => 'Paints',
        'Plumbing & Sanitary Materials' => 'Plumbing & Sanitary Materials',
        'Plumbing Fittings' => 'Plumbing Fittings',
        'Rebars' => 'Rebars',
        'Roofing Materials' => 'Roofing Materials',
        'Safety & PPE' => 'Safety & PPE',
        'Sand & Aggregates' => 'Sand & Aggregates',
        'Scaffolding Tools' => 'Scaffolding Tools',
        'Softscape' => 'Softscape',
        'Site Consumables & Utilities' => 'Site Consumables & Utilities',
        'Special Orders' => 'Special Orders',
        'Steel' => 'Steel',
        'Tiles & Tiling Materials' => 'Tiles & Tiling Materials',
        'Tools & Equipment' => 'Tools & Equipment',
        'Windows' => 'Windows',
        'Woods' => 'Woods',
        'Solar Appliances' => 'Solar Appliances',
    ],

    /*
    |--------------------------------------------------------------------------
    | Units of Measurement
    |--------------------------------------------------------------------------
    |
    | Standard units of measurement for materials.
    | These are used when specifying quantities in procurement requests.
    |
    */
    'units_of_measurement' => [
        '6_tyre_truck' => '6-Tyre Truck',
        'bags' => 'Bags (bags)',
        'boxes' => 'Boxes (boxes)',
        'buckets' => 'Buckets',
        'bundles' => 'Bundles (bundles)',
        'cartridges' => 'Cartridges',
        'cartons' => 'Cartons (cartons)',
        'cm' => 'Centimeters (cm)',
        'm3' => 'Cubic Meters (m³)',
        'cylinders' => 'Cylinders',
        'ft' => 'Feet (ft)',
        'gal' => 'Gallons (gal)',
        'g' => 'Grams (g)',
        'in' => 'Inches (in)',
        'kg' => 'Kilograms (kg)',
        'length' => 'Length (length)',
        'l' => 'Litres (litres)',
        'loads' => 'Loads (loads)',
        'lump_sum' => 'Lump Sum',
        'm' => 'Meters (m)',
        'ml' => 'Millilitres (ml)',
        'mm' => 'Millimeters (mm)',
        'packs' => 'Packs',
        'pairs' => 'Pairs (pairs)',
        'pcs' => 'Pieces (pcs)',
        'reams' => 'Reams',
        'rolls' => 'Rolls (rolls)',
        'sets' => 'Sets (sets)',
        'sheets' => 'Sheets (sheets)',
        'm2' => 'Square Meters (m²)',
        'ton' => 'Ton',
        'tons' => 'Tons (ton)',
        'trips' => 'Trips',
        'units' => 'Units (units)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Material Code Format
    |--------------------------------------------------------------------------
    |
    | Format for auto-generated material codes.
    | {year} - Current year (e.g., 2026)
    | {sequence} - Auto-incremented sequence number (padded to 4 digits)
    |
    */
    'code_format' => 'MAT-{year}-{sequence}',
    'code_sequence_padding' => 4,
];
