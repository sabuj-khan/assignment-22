<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Helper\JWTToken;
use Illuminate\View\View;
use Illuminate\Http\Request;

class UserController extends Controller
{


    function LoginPage():View{
        return view('pages.auth.login-page');
    }

    function RegistrationPage():View{
        return view('pages.auth.registration-page');
    }

    function SendOtpPage():View{
        return view('pages.auth.send-otp-page');
    }

    function VerifyOTPPage():View{
        return view('pages.auth.verify-otp-page');
    }

    function ResetPasswordPage():View{
        return view('pages.auth.reset-password-page');
    }

    function ProfilePage():View{
        return view('pages.dashboard.profile-page');
    }













    function userRegistration(Request $request){
        try{
            User::create($request->input());

            return response()->json([
                'status'=>'Success',
                'message'=>'You are has been registered'
            ]);
        }
        catch(Exception $error){
            return response()->json([
                'status'=>'Fail',
                'message'=>'Unauthorized from user registration'
            ], 401);
        }
    }


    function userLogin(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');

        $count = User::where('email', '=', $email)->where('password', '=', $password)->count();

        if($count ==1){
            $token = JWTToken::createJWTToken($email);

            return response()->json([
                'status'=>'Success',
                'message'=>'You have been logged in Successfully'
            ])->cookie('token', $token, 60*24*30);
        }else{
            return response()->json([
                'status'=>'Fail',
                'message'=>'Login Fail, Unauthorized Login function'
            ]);
        }
    }


    function sendOTPCode(Request $request){
        $email = $request->input('email');
        $otp = rand(1000, 9999);

        $count = User::where('email', '=', $email)->count();

        if($count == 1){
            // Send OTP code to user Email
            // Mail::to($email)->send(new OTPMail($otp));

            // Update Otp code in database
            User::where('email', '=', $email)->update(['otp'=>$otp]);

            return response()->json([
                'status'=>'Success',
                'message'=>'4 Digit OTP code has been sent to your email'
            ]);
        }else{
            return response()->json([
                'status'=>'Failed',
                'message'=>'Unauthorized from UserController'
            ]);

        }

    }


    function OTPVerification(Request $request){
        $email = $request->input('email');
       $otp = $request->input('otp');

       $count = User::where('email', '=', $email)->where('otp', '=', $otp)->count();

       if($count == 1){
            // OTP update
            User::where('email', '=', $email)->update(['otp'=>'0']);

            // New Token create
            $token = JWTToken::createJWTTokenforpassword($email);

            return response()->json([
                'status'=>'Success',
                'message'=>'OTP has been varified Successfully',
                'token'=>$token
            ]);

       }else{
            return response()->json([
                'status'=>'Failed',
                'message'=>'Unauthorized to varified OTP',
            ]);
       }
    }


    function resetPassword(Request $request){
        try{
            $email = $request->header('email');
            $password = $request->input('password');

            User::where('email', '=', $email)->update(['password'=>$password]);

            return response()->json([
                'status'=>'Success',
                'message'=>'Password has been reset Successfully'
            ]);
        }
        catch(Exception $error){
            return response()->json([
                'status'=>'Fail',
                'message'=>'Something went wrong'
            ]);
        }
    }





}
