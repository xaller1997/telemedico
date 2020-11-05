<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class HomeController
{
    private $pageHTML;
    protected $entityManager;
    protected $session;
    protected $notification;
    protected $logo = '<div class="imgcontainer">
        <a href="https://telemedi.co/pl/">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAe8AAABmCAMAAADVn+lbAAAAt1BMVEX///9MTEwAw3dISEhCQkLS0tJJSUk9PT1FRUUAwnRTU1OoqKjw8PAAwXF9fX3W1tZqamrq6uq+vr7FxcX39/c1NTXf39+EhIR1dXWMjIxsbGw6Ojrl5eVn1aNh0ZpWVlafn59hYWGF37inp6e1tbWVlZXf9uzt+/bBwcGZmZldXV3W9ejo+fLC79x12q41zI2n5smU4L0gyIO67Naa5MNCzY9s2avA79ut6M5O0Zg7z5GJ4bx/3bTuE2UiAAAQfklEQVR4nO1de0PiOBCnNPaBLc/yalGKFhAQXVF3db3v/7kumfSRpOljEU/dy+/+uKVN0jS/zGQymamNhoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCwv8Nt6vVOn91vSOQ3FD4zlg/Xd4fDs8XP3hmb6+em67rNp//WX1SxxQ+AOs717UBrvuWMb5+w5ebBPj6nZLxvwX7e7eZwn3exZdXz8zlpntQIv53YNe0GV6bdkzsLTsLmOsK3xsx3a57OLguJfYWX15Tum23eWhSre7eK5X+F+CVkGk3f+5Wq91P4N79iS/fUbrvX1ar1f6VEv722X1VeDd+uCDSe/prdwDCXxo7oNt9YEs17dtP66bCiUAIZpbmWyLhCd/uj7TYI8yDq0/posLp8AvE+yW78IL3Xgf8f7yYu69MwQtc0H5WK/g3xwURb5bXxu7uCuy1h4sn7jIo9F1D4Tvj9p6o78c6RS9tTsErfEeA1B5qmWFXWKG7dx/dIQl6n/DM4zD78l3du6I6L8RL/aLXfrcafr9WW5FmGVGtkp+NWctCYy/73Zv6k75XXP4zQPZZNa3uR8L37zolzwO9DoKzGm31WrpmWF9s1OSYIk0zs6k5aiFdR9NP7JAEDy6zyS7HrjbfY0urA3Reo60zUjLo1Orh58Kb6Jqmt9PfbRgFc/SJXcoD+H6qLteI9XktvkPTqEG3MRzUaAv4Nutogs+GyPcCBgF9rbXoD+SbqH77ok5Jp60NOSAg2OQvzsM6bX1fvrs68F1nUv93eCJ8/1Or6FX9oiIG6GjN9n357mMtZ1jbT+yRBPvaQkv337V26jn0ge+jFuHvy3fjutVqTb7W8v0H++/VO/xr/0++G85o9mm9KQD1r+1rlHx5h//8f8r3VwT4z+sodJgYR7rXFN9fBns42K5W03s4D/113EMU318G62Y9ub2EY/IjH6L4/jp4g8CGKgGn4Q/1Nup5KL6/DqjdXXUO8moncYzHQPH9hXAFES7lPtUn+z3iXcm3N4rCzXgaRqPcwUgh36TOktSR7Hm8/uYm/ufsfDoej5cRtw/uRUt8cXpeMgN7N7hH403UKT6q8Qbn4RR3IG4lz/dZJD8em93A256fOQUNn0XhdLzBr/YR50RrGqJYFly+ojGNRwczlfM922wDhCzLQiiYhwJ9BXzPNvOkznARisfO7QAFS6jtB6QQLhZ0U8fmmW8mF+eRdESdaDKkZVCgT+Uuk87YSoq0oNN5f8sQBZNc+7PreRBXNM12lKd8MDbi+7jl5Qf4a16AzftiZb1+jYNWj0UZ385GQ9nxioFa19xtKd/OUqjDH0p0iOd6iBmYWnpaStfp0aTDXjSsrSREIZqjrIhmGdM8KbO2YTGtaHjeiHz35kb+pZ0Qt5e1rSNxxp35FnO6aCBtcnqfzSsYY8WbcND4bq2jMTlK+J51gTkDz2YEI2GgNjsCMr5Hi7SOFdcZs/cHFuW7Dec0hhEPsLnB97wuf9Fa5Mhs09Z10jw98+iKk+JsHpNi0IYMc5nnu2Xkej7z43lq6Hr8D8SpgCieaeQ+LWC1Tn7icks1etGmLI5Qj+V/vdvv93+YWVTM9wBGVEeLcRiG44VJfiGfGQEJ32mdKa1DxoUbNeA7aIzxUy1Tm3dbiI5yEDWcrkWqWnN/Hl9EE75HmBIgsNXGrU99RNqy5ryQ9amM6shsLRa0dTSuwXcH3k/TTWvh+36L/rLm2YwbB3QOmC3fx92mk9kMaw5zbfzikwt47GhuCdXm+5/3B+Jxf377E1u9kO8RGRPN8mPLxRn4MLxMUEie745G6uiTDtTxnEEX6iyzEsA36luark3Peo7jjMIWGVpj600RVguTiFzsRVvJiaVHezDvOzCBvNGUyJm+YMt0KA+Wfz4j7czOSQ9Q1K7iuzOniqPd7zke/m8w0UkPspiYa0QnwGaE7zpOZ0rViHlyCX+j6SOy6NPVIU0xaux/xwnCtu3aP+ubb0V8ewuLil2GJSk6zEYpx7cDLA2Z4DdCIhcDA3xrc8NapNdGCyB8SoY7fZwzAW65tWBKLpljRskPCLsmY1V40JbORNV5fWQYWxLgUMa3A2/L6eeBj7VTOlXPTHjZcbZ6jGBJMlonX8MvKI35XVms62F//o/LJpK6h9qHo0V8h+QFdT5ukRCud9PxzvENE8LgZzwJoNKzVYDyzbaCZQtER8e6ganqgXpBLLlBbgY0BiCVmaEMUqhvOQ6o6Ap8Cz0PoZ7Pcxe10DDeOzoQIWHxEW8QG2YJa877sX6VSzicn2HjnSzYF24s2qmQ1yW8gO8ZGSQU8hcdX2c1mMg3rABi5Juz4EQp4Zvby7SpIcRXPUe8uqRr8ELYRG1wKZSuFz1KrbBlGKA837x89yxYBETTbxQmroJIRq1HddDJNfqaJvcLASy7A9hqTUI3JIza7uHi4eHulWYO1z0/KeCbTHi8pgpXI5OszskvkW+iu3VfrEN4s9KhpnwLk6JP7XI+3mREZJCZcTQQR9ztOybRqckzI6TlZymeFFYF36CYrJJwNlI+r7pnUO30brsVzffmvttBU8PtA/GuUyv9/pHevr0T8gxLUcC3nmcFw4PXTsRA5Fs+aFBnm+hl4NsQbGqgVgwf9IhiYAYTxDvvIyGLup6IGKliaHk/SgXfPVjefbFahjN4tTB3nVoUp89koLKMl+qU8EdKNwQxgRfOvcgmwx4O1uqFQsn57pDV28ybImDnJqMr8D0gday8/4MsAqmKHeTNMGLnwTwa5etlHHmWdAZC/5PLTiDsBrguFPMNdKLrfL0EoSU3cs70/Dw9DWKVfogPy95c+iWAPf2B/3nJlqaZw7UEXM43Vef50kRhpu8n8L205EJCbCiUGH4DGW0elS9hqoDkposHqPMgL0qdVjZ/QJ3rkvX0xizle1khpmA6GJrkDuigseTGe3EbG21Nstde/xN/3IMGO2HxFs/HftQOWJXzTUxPS5J/QUpbm/gHz7dHZF+i8+gcSa4D3+aNUKQrGzbgu5soZzDfUL51YlgmswKsbNkGaVQu37AMiJYgA2duFKzTsMjkTJZTYB2b4O5d4/Y55p4KOzn/zn3O4xXPgVrfdJHy7fjE0o6IY4FHnyWG5xsMcUkdL9KZOTKQasZCvo10LOFXN9+jGX5sYrCRWSrTSo3ZVtx/c5rJlKwxXHWzSN8TS1C0Rk6FKzveWx+oV+051tfgkBFdaiR43a6j0KV8w/zXWhJojI7l+S6tky6q1J8qniwVy3fCN6hUafNg/3lpGSnfTrfM31K47CcAy0TqSQOto31QStVTsremtltC8YUtCU5duzW3ZCV8w4kDD43dgPB8z+JDBnmdZG04mm8wuQpaj+Wb7tBl/o9y/3kvqDDXKN+ywA6481F8N/aH5LNrtp0tzpe2LIrVrfkFADnfsIEh52ISBIntJfAdlNRJV+z38W0U9IjKZnEUy8fyrX9YymRstWHzjHGfyfk+1EwmlvNNZfJchusC/xosctZSXof3rx3Lt9GVtx6Hqvx18t2IF3H7N7syfwTf4Bmt2lcKfGtFixyLd/FdsfOBHcIR6zfwnVqUsk5/zvoNgK0YZ4jJ+H68hGSkq+rgdbl9TuzZMgsGINhrc4n3PIdj+ab2eUWG37KoTC9nn3P7sWGVfV6oAKif4iNTk65q8L1/jU/L3GblyaiU72LNyELYj3WLNqks/pDvbD8GPnDJ/pvFdeH+O+dP5RR0yyjfRTucycmCnv4VxDaeBDX4fmPORt1DRQKa3N+yrDG6on8N+JE5oVgcLd9Rjbxl6tGV6N1BuX8NDjZRiX8NjIeW5M6H+ddSVPMdO9+Sk9EKwuV8w1VUEVou8A1rmVURtHk03yNUw1VtFmyk2+X+tZuqtsMCh2tHk3iHT4tKvp8ggtG9//nz3q0Rqyznu1flgiAQ+B7BMhiW1zma7wYJIpScj3Ggm/TcZcfI6XOO71Gr4nwMnPcSXmEBGX5oZnEV3ysa4gTOmNufblmwI6DgPBRGrkJYxfPQLQxr+dsfzzeoj6D8Q2E38oOusOr8m0aqlAg4icsz5qKAOzRar7RL70UV3+R+5ox5IOyXnoQX8E3PLSXWl3dWGM8ETclWM6bO8XyDEBqSmHSvk04xerA6F5oHa62Ub4hOMzTR7nL65/ElEGQk6jsaJlHvW3XHooLvdRrRRnFX9dGfovi1thiNCvDOW8NtUbwD9YHlVgHnWhsuuHiHo/imEXXWRCTlZhGgtPvnUKbLaf3eQq/iO37bXPyaGcSdgomkGTy112AVcOGxp0cF33vxnAQiX0raK+J7BkH3qM1S40Qty8jOsnPxijQYGI3ZYetBndRoPno/hgG8WQvWinQGW0tnBI8eq1hd5n06LV2bz0v3Y0QFQPD1nLHtz0j+gZGwCSfoeAnPuuOENMzyg78/V8E3ttZoQunqjR6RgrVe0l5h/HkEMf9WK0zujUKaO4IK4lsaibuJq7OlzRzJN7crjqPb0XgQX3QiH/IbmIi1ETxON5ZxDzpLXAmFlfHnEfBpoHYE2svrjyGbJAtRBN2tIT/OKnMimgxjlrhhT4IKvjG9VH0TE50Y5vAx/JIEhOL8kigO3TfRot1uT4yAJn2Y6bBJ8ktCrk7XCGi2h5ly+R6+8QNp1ocZzCe4R1pAM3wQm3d0Y9AygYF7sMAdgASX6vySKIjzUoKhttWHiJ4YaVlPpya9b+JnT+YmPNqQOmFOCinfmQl+F/O9S/T6vnks342BlmRikU+qxkaPtSyOPyfNtfJ1LGvDx5//Ad/8Ujzasq3HB7AWn7Y3aOlpmTgryamTPxZpaSJinL9mWF2mZW+T5ZfFj9a1j5ZuSSDD+tlmLLIHUb5/Ncv1+U1A3k++8ZqNAzZjEg/AcMKscOBtGIrGcJuvo+M6zMBChF9ux0qi+ZF4YAHZDcKm2AsDNj8U9yiYi9uomR+wKaRDMkGJPcZMKOBb/CZwp8v2HOsoTcgP7cdZZcmbBdv/4FsHv8SvqpLPtGUn3Y/J+r27uACp/uGW22tO17SCQsfKaLkwkYXns05SMrsbXg+MTcvMeylG06yOifwNPyEmuE5um9cnGajinJsZzFl71t9wgsykR+ZiLD2lnOikA6SERgv0h5bF5jiEgYW6Obf3YNxK0r+1xVLil418Az/bMAz8Zlr7v/kcJ4ktf2Z+kxPS7BsvZD/GfdLpYFc4XJxwI8+sj293ouVkMp9MptEovz9dSpLisfh0oulksphMlv18nfOl5HFnyzCvYkbhRjak3qgf4tZ93KNOgc/bG+EO+P5kmX5hot8ecw+I2lOZY2h2dr0ZLzfRYCY/A/FmuOH5fD6Z9vPfu/gYXLpcMhks1IzCvnC5L75AoGqdb/YpfFGAAZb+1SL4o2Ts5x323De8XppiaLrCdwPENNkXRGh3V01b/JQmkX/3FUR6fWVzyl7hO4J+xMd2Xfjz37lvcUF2oe0eXi9/u+/6MpvCFwH/R4NzB9w0LbxpQ9yybas/LPrtsbpPI1hs93dOXa8vk9u22zzuq+gKXwsvz0SZY339Ko0x31/C7ebzk/ojk38H1rsfVxcPP3ZFfO4en55+Fd5VUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUFBQUPhz/Avjyk2g75dBtwAAAABJRU5ErkJggg==" alt="Avatar" class="avatar">
        </a>
    </div>';

    public function __construct(string $title = 'Home', string $content = null, EntityManagerInterface $entityManager = null) {
        $this->session = new Session();
        $this->entityManager = $entityManager;

        $this->pageHTML = '
            <!DOCTYPE html>
            <html lang="pl">
                <head>
                    <title>' . $title . ' | Telemedico</title>
                    <link href="style.css" rel="stylesheet"/>
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link rel="icon" href="favicon.png">
                </head>
                <body>
                    <ul class="horizontal">
                      <li>
                           <a ' . (($title === 'Home') ? 'class="active"' : '') . ' href="/home">Home</a>
                      </li>
                      <li ' . (($this->isLogged()) ? 'class="disabled-li"' : '') . '>
                          <a ' . (($title === 'Logowanie') ? 'class="active"' : '') . ' href="/login">Logowanie</a>
                      </li>
                      <li ' . (($this->isLogged()) ? 'class="disabled-li"' : '') . '>
                          <a ' . (($title === 'Rejestracja') ? 'class="active"' : '') . ' href="/register">Rejestracja</a>
                      </li>
                      ' . ($this->isLogged() ? '
                          <li class="float-right"><a href="/logout">Wyloguj</a></li>
                      ' : '') . '
                    </ul>
                    ' . $content . '
                    ' . ($this->isLogged() ? '
                      <div class="main-container">
                        ' . ($this->logo) . '
                        <h1 class="center-text">Witaj ' . ($this->getUser()->getEmail()) . '!</h1>
                      </div>
                    ' : '') . '
                </body>
            </html>
        ';

        return new Response($this->pageHTML);
    }

    public function isLogged(): bool
    {
        if (!empty($this->session->get('user'))) {
            return true;
        }
        return false;
    }

    public function getUser()
    {
        return $this->session->get('user');
    }

    public function redirect(string $url): RedirectResponse
    {
        return new RedirectResponse($url);
    }

    public function logout()
    {
        $this->session->remove('user');
        return $this->redirect('/login');
    }
}