<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Tu código de acceso a MemoCar</title>
</head>
<body style="margin:0;padding:0;background:#F1F4EE;font-family:Figtree,Segoe UI,Helvetica,Arial,sans-serif;color:#17191E">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#F1F4EE;padding:32px 16px">
  <tr><td align="center">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:480px;background:#FFFFFF;border-radius:14px;overflow:hidden;border:1px solid #DDE2D8">
      <tr><td style="background:#000000;padding:18px 28px">
        <table role="presentation" cellspacing="0" cellpadding="0"><tr>
          <td style="width:34px;height:34px;background:#76B900;border-radius:8px;text-align:center;vertical-align:middle">
            <img src="{{ url('/icons/icon-192.png') }}" width="34" height="34" alt="" style="display:block;border-radius:8px">
          </td>
          <td style="padding-left:10px;font-size:20px;font-weight:800;color:#FFFFFF;font-family:Archivo,Segoe UI,Helvetica,Arial,sans-serif">MemoCar</td>
        </tr></table>
      </td></tr>
      <tr><td style="padding:32px 28px 8px">
        <p style="margin:0 0 6px;font-size:16px">Hola, {{ $user->name }}:</p>
        <p style="margin:0 0 22px;font-size:16px;line-height:1.5;color:#3D424A">Usa este código para entrar a MemoCar. Vence en {{ $minutes }} minutos.</p>
        <div style="margin:0 0 24px;padding:18px 0;background:#F1F4EE;border-radius:10px;text-align:center;font-family:'JetBrains Mono',Consolas,Menlo,monospace;font-size:34px;font-weight:700;letter-spacing:10px;color:#17191E">{{ $code }}</div>
        <p style="margin:0 0 14px;font-size:15px;color:#3D424A">O entra directamente con un toque:</p>
        <table role="presentation" cellspacing="0" cellpadding="0"><tr><td style="border-radius:8px;background:#76B900">
          <a href="{{ $link }}" style="display:inline-block;padding:14px 26px;font-size:16px;font-weight:700;color:#0B0D0B;text-decoration:none;border-radius:8px">Entrar a MemoCar</a>
        </td></tr></table>
      </td></tr>
      <tr><td style="padding:24px 28px 28px;font-size:13px;line-height:1.5;color:#5F656C">
        Si no intentaste entrar, ignora este correo: nadie podrá acceder sin este código.<br>
        Nunca te pediremos este código por teléfono ni por mensaje.
      </td></tr>
    </table>
    <p style="margin:16px 0 0;font-size:12px;color:#5F656C">memocar.upsofte.com · El historial de mantenimiento de tus carros</p>
  </td></tr>
</table>
</body>
</html>
