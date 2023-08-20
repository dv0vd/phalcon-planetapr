<?php

use Phalcon\Mvc\Controller;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

class IndexController extends Controller
{
    
    public function indexAction() {
      $this->view->title = "Турагентство Белгород | Планета путешествий и развлечений";
      $this->view->description = "Бронирование туров, жилья, отелей, заказ авиа, ж/д и автобусных билетов, организация пассажирских перевозок. С нетерпением ждём Вас!";
    }

    public function askAction() {
        $request = $this -> request;
        if(!$request -> isPost()) {
          $this->response->redirect('/');
        }
        if ($request -> isAjax() && $this->security->checkToken()) {
            $result = array();
            $token = ['tokenKey' => $this->security->getTokenKey(), 'token' => $this->security->getToken()];
            array_push($result, $token);
            $validation = new Validation();
            $validation -> add(
                'email', new Email([
                    'message' => 'Введите корректную электронную почту!',
                ])
            );
            $validation -> add(
                'email', new PresenceOf([
                    'message' => 'Введите почту!',
                ])
            );
            $validation -> add(
                'name', new PresenceOf([
                    'message' => 'Введите имя!',
                ])
            );
            $validation -> add(
                'question', new PresenceOf([
                    'message' => 'Введите вопрос!',
                ])
            );
            $messages = $validation -> validate($_POST);
            if (count($messages)) {
                foreach ($messages as $message) {
                  array_push($result, $message);
                }
                return json_encode($result);
            }
            $name = $request->getPost('name');
            $email = $request->getPost('email');
            $question = $request->getPost('question');
            require_once APP_PATH."/libraries/PHPMailer/PHPMailer.php";
            require_once APP_PATH."/libraries/PHPMailer/SMTP.php";
            require_once APP_PATH."/libraries/PHPMailer/Exception.php";
            try {
                $mail = new PHPMailer;
                $mail->CharSet = 'UTF-8';
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->Host = 'ssl://smtp.mail.ru';
                $mail->Port = 465;
                $mail->Username = '';
                $mail->Password = '';
                $mail->From = ('planetapr@mail.ru');
                $mail->FromName = ('Вопрос с сайта');
                $mail->addAddress('planetapr@mail.ru');
                $mail->Subject = 'Вопрос с сайта';
                $mail->isHTML(true);
                $mail->Body = "<p>Имя: $name</p><p>Почта: $email</p><center><p><b>Вопрос</b></p></center><p>$question</p>";
                $mail->send();
                $message = ['result' => 'success'];
                array_push($result, $message);
                return json_encode($result);
            } catch (Exception $e) {
                $message = ['result' => 'error', 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"];
                array_push($result, $message);
                return json_encode($result);
            }
        } else {
          $message = ['result' => 'error', 'message' => "Token error"];
        }
    }

    public function contactsAction() {
      $this->view->title = 'Контакты, адрес, режим работы | Планета путешествий и развлечений';
      $this->view->description = "Телефоны: +7(920)573-93-81, +7(920)561-46-86. Email: planetapr.su. Адрес: г. Белгород ул. Гостёнская 14 офис 7. Режим работы: Пн-Пт 10.00 - 18.00, перерыв 13.30 - 14.00.";
    }
    
    public function transportAction() {
      $this->view->title = 'Пассажирские перевозки | Планета путешествий и развлечений';
      $this->view->description = "Аренда комфортабельных автобусов и микроавтобусов по гибким ценам! Путешествие для больших групп людей. Выгодные условия, опытные водители и безопасное вождение!";
    }
    
    public function toursAction() {
      $this->view->title = 'Горящие туры и путёвки | Планета путешествий и развлечений';
      $this->view->description = "Забронировать туры по всем популярным направлёниям, лучшие предложения и низкие цены. С нетерпением ждём Вас!";
    }
    
    public function newsAction() {
      $this->view->title = 'Новости | Планета путешествий и развлечений';
      $this->view->description = "Свежие новости туристической индустрии в России и мире, горящие туры и путёвки!";
    }

}