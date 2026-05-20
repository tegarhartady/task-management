<?php

namespace Database\Seeders;

use App\Models\Brief;
use App\Models\BriefAttachment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BriefSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Get first 2 users (admin and supervisor)
    $users = User::limit(2)->get();

    if ($users->count() < 2) {
      $this->command->error('Minimal 2 users diperlukan.');
      return;
    }

    $creator = $users[0];
    $assigned = $users[1];

    // Brief 1
    Brief::create([
      'title' => '[Reels] Yang keliaran sepele ternyata gangggu',
      'brand' => 'SUPERNATA',
      'type' => 'REEL',
      'status' => 'DRAFT',
      'hook' => 'Yang bikin iled tuh kadang bukan bentuknya',
      'concept' =>
        'Konsep ini menceritakan apa kualitas furnitur dalam jangka panjang bukan ditentukankan oleh desain, tapi oleh finishing permukaan.',
      'visual_direction' => 'Permukaaan furnitur caling sering kena permakaan harian (air dan gesekan)',
      'voiceover' => 'Tapi pas kena cahaya, sok tiba-tiba keliatan beleng, kusam, atau ada bintik-bintik.',
      'created_by' => $creator->id,
      'assigned_to' => null,
      'comments' => 0,
      'is_ai' => false,
    ]);

    // Brief 2
    Brief::create([
      'title' => '[IGR] Nyesel kenapa aku baru tahu bisa preview animasi pintu sebel',
      'brand' => 'SUPERNATA',
      'type' => 'REEL',
      'status' => 'DRAFT',
      'hook' => null,
      'concept' => null,
      'visual_direction' => null,
      'voiceover' => null,
      'created_by' => $creator->id,
      'assigned_to' => null,
      'comments' => 0,
      'is_ai' => true,
    ]);

    // Brief 3
    Brief::create([
      'title' => '[IGS] Fix pain',
      'brand' => 'DEKORNATA',
      'type' => 'IGS',
      'status' => 'TAKEN',
      'hook' => null,
      'concept' => null,
      'visual_direction' => null,
      'voiceover' => null,
      'created_by' => $creator->id,
      'assigned_to' => $assigned->id,
      'comments' => 1,
      'is_ai' => false,
    ]);

    // Brief 4
    Brief::create([
      'title' => '[POST] Basic human needs',
      'brand' => 'DEKORNATA',
      'type' => 'POST',
      'status' => 'SUBMITTED',
      'hook' => 'Furniture yang memenuhi kebutuhan dasar',
      'concept' => 'Fokus pada furniture yang fungsional dan memenuhi kebutuhan manusia modern.',
      'visual_direction' => 'Minimalis, clean, modern aesthetic dengan warna neutral.',
      'voiceover' => 'Furniture yang dirancang untuk memenuhi kebutuhan sehari-hari Anda.',
      'created_by' => $creator->id,
      'assigned_to' => $assigned->id,
      'comments' => 3,
      'is_ai' => false,
    ]);

    // Brief 5
    Brief::create([
      'title' => '[CAROUSEL] Koleksi Terbaru 2026',
      'brand' => 'CRAFTNATA',
      'type' => 'CAROUSEL',
      'status' => 'APPROVED',
      'hook' => 'Lihat koleksi furniture terbaru kami',
      'concept' => 'Showcase koleksi furniture terbaru dengan fokus pada inovasi dan design.',
      'visual_direction' => 'Colorful, vibrant dengan showcase produk yang eye-catching.',
      'voiceover' => 'Temukan furniture impian Anda di koleksi terbaru kami.',
      'created_by' => $creator->id,
      'assigned_to' => $assigned->id,
      'comments' => 5,
      'is_ai' => false,
    ]);

    $this->command->info('5 briefs seeded successfully!');
  }
}
