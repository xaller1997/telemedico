<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends HomeController
{
    public function registerAction(Request $request)
    {
        if ($request->getMethod() === 'POST' && $this->validateRegistration($request)) {
            return $this->redirect('/login');
        }

        if ($this->isLogged()) {
            return $this->redirect('/home');
        }

        return parent::__construct('Rejestracja', '
            <form action="/register" method="post">
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
                        
                      <label for="password-repeat"><b>Powtórz hasło:</b></label>
                      <input type="password" placeholder="Wpisz ponownie hasło" name="password-repeat" minlength="8" required>
                      <hr>
                      <p>Tworząc konto, zgadzasz się na nasze <a href="#">warunki i prywatność</a>.</p>
                    
                      <button type="submit">Zarejestruj</button>
                </div>
                
                 <div class="container signin">
                    <p>Posiadasz już konto? <a href="/login">Zaloguj się</a>.</p>
                 </div>
            </form>
        ');
    }

    public function validateRegistration(Request $request)
    {
        // Check if empty:
        if (
            empty($request->get('email')) ||
            empty($request->get('password')) ||
            empty($request->get('password-repeat'))
        ) {
            $this->notification = 'Musisz wypełnić wszystkie pola!';
            return false;
        }
        // Check passwords length:
        if (
            strlen($request->get('password')) < 8 ||
            strlen($request->get('password-repeat')) < 8
        ) {
            $this->notification = 'Minimalna długość hasła to 8 znaków!';
            return false;
        }
        // Compare passwords:
        if($request->get('password') !== $request->get('password-repeat')) {
            $this->notification = 'Hasła muszą być identyczne!';
            return false;
        }
        // Check if e-mail exists:
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $request->get('email')
        ]);

        if (is_object($user)) {
            $this->notification = 'Adres e-mail jest zajęty!';
            return false;
        }

        $user = new User();
        $user->setEmail($request->get('email'));
        $user->setPassword($request->get('password'));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return true;
    }
}