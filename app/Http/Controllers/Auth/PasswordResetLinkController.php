<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Notifications\SmsCenter;
use Illuminate\Support\Facades\Validator;

class PasswordResetLinkController extends Controller
{

    public function create(Request $request)
    {
        $request->merge([
            'phone' => User::toDigit($request->phone),
        ]);

        $validatedData = $request->validate([
            'phone' => 'exists:users,phone',
        ]);

        return !empty($validatedData['phone']) ?
            redirect()->route('password.phone', [
                'phone' => $request->phone,
            ]) :
            view('auth.forgot-password');
    }

    public function createForm(Request $request)//: string
    {
        $validator = Validator::make(['phone' => $request->phone], [
            'phone' => 'required|exists:users,phone',
        ]);

        if ($validator->fails()) {
            return redirect()->route('password.request', ['failed' => 1]);
        }

        $todayEntries = DB::table('sent_pin')
            ->where('phone', $request->phone)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at','desc')
            ->get();

        $lastEntry = $todayEntries->first();

        $this->checkAndChangeActive(['phone' => $request->phone]);

        $lastActiveEntry = DB::table('sent_pin')
            ->where('phone', $request->phone)
            ->where('is_active', 1)
            ->orderBy('created_at','desc')
            ->first();

        $endPinTime = !empty($lastActiveEntry) ? Carbon::createFromTimeString($lastActiveEntry->created_at)->addMinutes
        (5) : false;

        return view('auth.forgot-password', [
            'phone' => $request->phone,
            'pin_created' => $lastActiveEntry->created_at ?? false,
            'pin_ended' => $endPinTime,
            'last_active_entry' => $lastActiveEntry,
            'attempts' => 10 + 1 - $todayEntries->count(),
            'pin_activity_time' => Carbon::now()->diff($endPinTime)->format('%I:%S'),
        ]);
    }

    public function insertPinOrFail($todayEntries, array $array)
    {
        $lastEntry = $todayEntries->first();

        $this->checkAndChangeActive(['phone' => $array['phone']]);

        if ($todayEntries->count() < 10 && (empty($lastEntry) || $lastEntry->is_active == 0)) {
            DB::table('sent_pin')->insert([
                'phone' => $array['phone'],
                'pin_code' => $array['pin'],
                'created_at' => Carbon::now(),
                'is_active' => 1,
            ]);
        } else {
            return false;
        }

        return true;
    }

    public function sendSmsAjax(Request $request) {
        $request->validate([
            'phone' => 'required|exists:users,phone',
        ]);

        $todayEntries = DB::table('sent_pin')
            ->where('phone', $request->phone)
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at','desc')
            ->get();

        $insertData = [
            'phone' => $request->phone,
            'pin' => $this->generatePin(),
        ];

        if ($this->insertPinOrFail($todayEntries, $insertData)) {
            $user = User::where('phone', $request->phone)->first();

            $user->notify(new SmsCenter([
                'msg' => "Код для восстановления:\n",
                'password' => $insertData['pin']
            ]));

            return json_encode([
                'success' => true,
            ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        }
    }

    public function generatePin() {
        return str_pad(random_int(100, 9999), 4, 0, STR_PAD_LEFT);
    }

    public function checkAndChangeActive($array) {
        $todayEntries = $this->getTodayEntries($array['phone']);
        $lastEntry = $todayEntries->first();

        if (!empty($lastEntry) && $lastEntry->is_active == 1) {
            $endPinTime = Carbon::createFromTimeString($lastEntry->created_at)->addMinutes(5);

            if (Carbon::now()->between($lastEntry->created_at, $endPinTime) === false) {
                $lastEntry->is_active = 0;

                DB::table('sent_pin')
                    ->where('id', $lastEntry->id)
                    ->update(['is_active' => 0]);

                return true;
            }
        }

        return false;
    }

    public function getTodayEntries($phone) {
        return DB::table('sent_pin')
                ->where('phone', $phone)
                ->whereDate('created_at', Carbon::today())
                ->orderBy('created_at','desc')
                ->get();
    }

    public function store(Request $request) {
        $request->merge([
            'phone' => User::toDigit($request->phone),
        ]);

        $request->validate([
            'phone' => 'required|exists:users,phone',
            'pin' => 'required|digits:4|exists:sent_pin,pin_code',
            'new_password' => 'required',
        ]);

        $this->checkAndChangeActive(['phone' => $request->phone]);

        $lastActiveEntry = DB::table('sent_pin')
            ->where('phone', $request->phone)
            ->where('is_active', 1)
            ->orderBy('created_at','desc')
            ->first();


        if (!empty($lastActiveEntry)) {
            $user = User::where('phone', $request->phone)->first();

            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            $user->save();

            DB::table('sent_pin')
                ->where('phone', $request->phone)
                ->where('is_active', 1)
                ->update(['is_active' => 0]);

            //todo add to password_resets

            return redirect()->route('login');
        }
    }
}
