<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends HomeController
{
    public function loginAction(Request $request)
    {
        if (($request->getMethod() === 'POST' && $this->validateLogin($request)) || $this->isLogged()) {
            return $this->redirect('/home');
        }
        return parent::__construct('Logowanie', '
            <form action="/login" method="post">
                ' . ($this->logo) . '
                
                <div class="container">
                    ' . (($this->notification) ? '
                    <div class="alert">
                        <span class="closebtn" onclick="this.parentElement.style.display=\'none\';">&times;</span>
                        ' .  $this->notification . '
                    </div>' : '') . '
                      
                    <label for="email"><b>Adres e-mail:</b></label>
                    <input type="email" placeholder="Wpisz adres e-mail" name="email" required>
                    
                    <label for="password"><b>Hasło:</b></label>
                    <input type="password" placeholder="Wpisz hasło" name="password" minlength="8" required>
                    
                    <button type="submit">Zaloguj</button>
                </div>
                
                <div class="container signin">
                    <p>Nie posiadasz jeszcze konta? <a href="/register">Zarejestruj się</a>.</p>
                </div>
            </form>
        ');
    }

    public function validateLogin(Request $request)
    {
        // Check if empty:
        if (empty($request->get('email')) || empty($request->get('password'))) {
            $this->notification = 'Musisz wypełnić wszystkie pola!';
            return false;
        }
        // Check password length:
        if (strlen($request->get('password')) < 8) {
            $this->notification = 'Minimalna długość hasła to 8 znaków!';
            return false;
        }
        // Check if user with e-mail and password exists:
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $request->get('email'),
        ]);

        if (!is_object($user) || !password_verify($request->get('password'), $user->getPassword())) {
            $this->notification = 'Zły e-mail lub hasło!';
            return false;
        }

        $this->session->set('user', $user);
        $user->setToken(bin2hex(random_bytes(125)));
        $this->entityManager->flush();

        return true;
    }
}