<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Models\Portal;
use App\Requests\CustomRequestHandler;
use App\Response\CustomResponse;
use App\Controllers\Auth\GenerateTokenController;
use Respect\Validation\Validator as v;
use App\Validation\Validator;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;

class AuthController
{
  // protected $view;
  protected $customResponse;
  protected $validator;
  protected $mailer;

  public function __construct(SmtpMailer $mailer)
  {
    $this->customResponse = new CustomResponse();
    $this->validator = new Validator();
    $this->mailer = $mailer;
  }

  public function postSignUp($request, $response) {
    // Validate request passed parameters
    $this->validator->validate($request, [
      "name"=>v::notEmpty(),
      "email"=>v::notEmpty()->email(),
      "password"=>v::notEmpty(),
    ]);

    if($this->validator->failed())
    {
      $responseMessage = $this->validator->errors;
      return $this->customResponse->is422Response($response, $responseMessage);
    }

    // Validate if email addrress already exists
    if($this->EmailExists(CustomRequestHandler::getParam($request, "email")))
    {
      $responseMessage = "This email alreaedy exists";
      return $this->customResponse->is400Response($response, $responseMessage);
    }

    $activCode = md5('yourSalt' . date('Ymdhis'));

    $user = User::create([
      'name' => CustomRequestHandler::getParam($request, "name"),
      'email' => CustomRequestHandler::getParam($request, "email"),
      'password' => password_hash(CustomRequestHandler::getParam($request, "password"), PASSWORD_DEFAULT),
      'activ_code' => $activCode
    ]);

    $portal = Portal::create([
      'name' => $_SERVER['HTTP_HOST']
    ]);

    $mail = new Message;
    $mail->setFrom($_ENV['MAIL_USERNAME'])
      ->addTo($request->getParam('email'))
      ->setSubject('Plaease confirm your email')
      ->setHTMLBody("Hello, to confirm this Email insert this code: <br />
      <ol>
      <li>Go to " . $_ENV['BASE_URL'] . "/auth/confirm.</li>
      <li>Insert the following code " . "<b>" . $activCode. "</b>" . " to verify your registration.</li>
      </ol>");

    $this->mailer->send($mail);

    $responseMessage = GenerateTokenController::generateToken(CustomRequestHandler::getParam($request, "email"));

    $_SESSION['email'] = $request->getParam('email');

    return $this->customResponse->is201Response($response, $responseMessage);
  }

  public function postSignIn($request, $response) {
    $this->validator->validate($request, [
      "email"=>v::notEmpty()->email(),
      "password"=>v::notEmpty(),
    ]);

    if($this->validator->failed())
    {
      $responseMessage = $this->validator->errors;
      return $this->customResponse->is400Response($response, $responseMessage);
    }

    $verifyAccount = $this->verifyAccount(
      CustomRequestHandler::getParam($request, "email"), CustomRequestHandler::getParam($request, "password")
    );

    if($verifyAccount == false)
    {
      $responseMessage = "Invalid email or password";
      return $this->customResponse->is400Response($response, $responseMessage);
    }

    $responseMessage = GenerateTokenController::generateToken(CustomRequestHandler::getParam($request, "email"));

    return $this->customResponse->is201Response($response, $responseMessage);
  }

  public function EmailExists($emailAddress)
  {
    $count = User::where("email", $emailAddress)->count();

    if($count == 0)
    {
      return false;
    } else {
      return true;
    }
  }

  public function verifyAccount($email, $password)
  {
    $providedPassword = "";

    $count = User::where(["email" => $email])->count();

    if($count == false)
    {
      return false;
    }

    $getUser = User::where("email", $email)->get();

    foreach($getUser as $userInfo)
    {
      $providedPassword = $userInfo->password;
      $active = $userInfo->activ;
    }

    if ($active == 0){
      return false;
    }

    $verufyPassword = password_verify($password, $providedPassword);

    if($verufyPassword == false)
    {
      return false;
    }

    return true;
  }

  public function confirmEmail($request, $response)
  {
    if (!$request->getParam('code')) {
        return $response->withRedirect($this->router->pathFor('home'));
    }

    $user = User::where('activ_code', $request->getParam('code'))->first();
    $user->activ = 1;
    $user->save();

    $responseMessage = "Confirmation was successful";

    $_SESSION['email'] = $user->value('email');
  
    return $this->customResponse->is201Response($response, $responseMessage);
  }
}