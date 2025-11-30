<?php
namespace App\Http\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Support\Facades\Validator;

class OnboardingConversation extends Conversation
{
    public function askName()
    {
        $this->ask('What is your name?', function (Answer $answer) {
            $this->say('Nice to meet you ' . $answer->getText());
        });
    }
    
    public function askEmail()
    {
        $this->ask('What is your email?', function (Answer $answer) {

            $validator = Validator::make(['email' => $answer->getText()], [
                'email' => 'email',
            ]);

            if ($validator->fails()) {
                return $this->repeat('That doesn\'t look like a valid email. Please enter a valid email.');
            }

            $this->bot->userStorage()->save([
                'email' => $answer->getText(),
            ]);

            $this->askMobile();
        });
    }

    public function askMobile()
    {
        $this->ask('Great. What is your mobile?', function (Answer $answer) {
            $this->bot->userStorage()->save([
                'mobile' => $answer->getText(),
            ]);

            $this->say('Great!');
        });
    }

    public function run()
    {
        $this->askName();
    }
}