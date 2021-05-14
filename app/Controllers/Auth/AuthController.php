<?php

namespace App\Controllers\Auth;

use App\Models\User;
use App\Requests\CustomRequestHandler;
use App\Response\CustomResponse;
use App\Controllers\Auth\GenerateTokenController;
use Respect\Validation\Validator as v;
use App\Validation\Validator;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use App\Helpers\EmailExists;
use App\Helpers\PortalExists;
use App\Helpers\VerifyAccount;
use App\Helpers\CheckActivationCode;

class AuthController
{
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
    if(EmailExists::checkEmail(CustomRequestHandler::getParam($request, "email")))
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

    // Check if portal exists already
    PortalExists::checkPortal($_SERVER['HTTP_HOST']);

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

    $verifyAccount = VerifyAccount::verifyAccount(
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

  public function confirmEmail($request, $response)
  {
    if (!$request->getParam('code')) {
      $responseMessage = "Invalid activation code";
      return $this->customResponse->is400Response($response, $responseMessage);
    }

    $checkCode = CheckActivationCode::checkCode($request->getParam('code'));

    if($checkCode == false)
    {
      $responseMessage = "Invalid activation code";
      return $this->customResponse->is400Response($response, $responseMessage);
    };

    $user = User::where('activ_code', $request->getParam('code'))->first();
    $user->activ = 1;
    $user->save();

    $responseMessage = "Confirmation was successful";

    $_SESSION['email'] = $user['email'];

    return $this->customResponse->is201Response($response, $responseMessage);
  }
}