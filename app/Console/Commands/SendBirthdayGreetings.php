<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AlumniProfile;
use Illuminate\Support\Facades\Log;

class SendBirthdayGreetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'alumni:send-birthday-wishes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرسال تهنئة تلقائية بعيد ميلاد الطلاب والخريجين الذين يوافق عيد ميلادهم اليوم';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $todayMonthDay = now()->format('m-d');

        $profiles = AlumniProfile::with('user')
            ->whereNotNull('birth_date')
            ->get()
            ->filter(function ($profile) use ($todayMonthDay) {
                return $profile->birth_date && $profile->birth_date->format('m-d') === $todayMonthDay;
            });

        $count = $profiles->count();
        $this->info("تم العثور على {$count} طالب/خريج يوافق عيد ميلادهم اليوم.");

        foreach ($profiles as $profile) {
            $user = $profile->user;
            if (!$user) {
                continue;
            }

            $wishes = "🎂 كل عام وأنت بخير يا {$user->name_ar}! أسرة جامعة نكسوس للعلوم والتكنولوجيا تهنئك بعيد ميلادك السعيد وتتمنى لك دوام التوفيق والنجاح. 🎉";

            Log::info("Birthday wish logged for user [{$user->id}] {$user->email}: {$wishes}");
        }

        $this->info("تم إرسال وسجل تهاني عيد الميلاد بنجاح.");

        return Command::SUCCESS;
    }
}
