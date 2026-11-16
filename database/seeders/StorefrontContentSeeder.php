<?php

namespace Database\Seeders;

use App\Models\StorefrontArticle;
use App\Models\StorefrontCollection;
use App\Models\StorefrontRegion;
use App\Models\StorefrontTestimonial;
use Illuminate\Database\Seeder;

class StorefrontContentSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['Rajasthan', 'rajasthan', 'Jaipur blue pottery, puppets, and exquisite marble crafts.', 'https://images.unsplash.com/photo-1599661046289-e31897846e41?auto=format&fit=crop&w=900&q=85'],
            ['Kashmir', 'kashmir', 'Luxurious Pashmina shawls and intricate walnut wood carvings.', 'https://images.unsplash.com/photo-1566837497312-7be4a0493241?auto=format&fit=crop&w=900&q=85'],
            ['Gujarat', 'gujarat', 'Traditional Lippan art, Kutch embroidery, and beadwork.', 'https://images.unsplash.com/photo-1606293926075-69a00dbfde81?auto=format&fit=crop&w=900&q=85'],
            ['West Bengal', 'west-bengal', 'Terracotta temples, Kantha textiles and ancient Dhokra art.', 'https://images.unsplash.com/photo-1558431382-27e303142255?auto=format&fit=crop&w=900&q=85'],
        ];
        foreach ($regions as $i => $r) {
            StorefrontRegion::updateOrCreate(['slug' => $r[1]], ['name' => $r[0], 'description' => $r[2], 'image_url' => $r[3], 'sort_order' => $i]);
        }
        foreach ([
            ['Wedding Gifts', 'wedding-gifts', 'Timeless gifts for a lifetime of cherished memories.', 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?auto=format&fit=crop&w=1100&q=85'],
            ['Festival Collection', 'festival-collection', 'Authentic decor and artisanal pieces for joyful celebrations.', 'https://images.unsplash.com/photo-1604423079515-20b8f82d026c?auto=format&fit=crop&w=1100&q=85'],
        ] as $i => $c) {
            StorefrontCollection::updateOrCreate(['slug' => $c[1]], ['name' => $c[0], 'description' => $c[2], 'image_url' => $c[3], 'sort_order' => $i]);
        }
        foreach ([['Sudhanshu Roy', 'Verified buyer', 'Beautiful quality! The craft is even more stunning in person. Truly a masterpiece of Indian art.'], ['Priya Sharma', 'Interior stylist', 'Amazing craftsmanship. Supporting local artisans while getting such high-quality heritage decor is a win-win.'], ['Arjun Mehta', 'Verified buyer', 'Soft, authentic and full of history—you can feel the heritage in every detail.']] as $i => $t) {
            StorefrontTestimonial::updateOrCreate(['customer_name' => $t[0]], ['customer_title' => $t[1], 'quote' => $t[2], 'rating' => 5, 'sort_order' => $i]);
        }
        foreach ([
            ['Top 10 Indian Handicrafts for a Modern Living Space', 'top-indian-handicrafts-modern-home', 'Artisan Heritage', 'Explore how traditional Indian crafts are evolving for contemporary homes.', 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&w=1000&q=85'],
            ['7 Sustainable Decor Ideas From Rural Villages', 'sustainable-decor-rural-villages', 'Conscious Decor', 'Bring life to your space with eco-friendly handmade elements.', 'https://images.unsplash.com/photo-1618221195710-dd6b41faaea6?auto=format&fit=crop&w=1000&q=85'],
            ['Preparing Your Home for the Festival Season', 'home-festival-season', 'Seasonal Events', 'Discover meaningful ways to light up your celebrations.', 'https://images.unsplash.com/photo-1604423079515-20b8f82d026c?auto=format&fit=crop&w=1000&q=85'],
        ] as $a) {
            StorefrontArticle::updateOrCreate(['slug' => $a[1]], ['title' => $a[0], 'category' => $a[2], 'excerpt' => $a[3], 'image_url' => $a[4], 'published_at' => now()]);
        }
    }
}
