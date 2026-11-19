<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Iniciar Sesión - Podología Ana Ramírez</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        podo: {
                            blue: '#005B94',
                            dark: '#0A2540',
                            mint: '#2EC4B6',
                            cyan: '#00B4D8',
                            light: '#F8FAFC'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="flex min-h-full items-center justify-center p-4 sm:p-6 bg-slate-100 font-sans antialiased text-slate-800">

    <div class="w-full max-w-sm space-y-6">
        
        <!-- TARJETA PRINCIPAL DE LOGIN -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xl overflow-hidden transition-all">
            
            <!-- ENCABEZADO CON EMBLEMA OFICIAL -->
            <div class="bg-gradient-to-br from-[#0A2540] to-[#005B94] p-8 text-center text-white">
                <div class="flex justify-center mb-3">
                    <div class="w-20 h-20 rounded-full border-2 border-[#2EC4B6] shadow-lg p-1 bg-[#0A2540] flex items-center justify-center">
                        <img src="assets/img/logo-badge.png" alt="Emblema Podología Ana Ramírez" class="w-full h-full object-contain">
                    </div>
                </div>

                <h1 class="text-xl font-black tracking-tight text-white">Podología Ana Ramírez</h1>
                <p class="text-[11px] uppercase tracking-wider text-[#2EC4B6] font-bold mt-0.5">Salud Podológica & Biomecánica</p>
            </div>

            <!-- CUERPO DEL FORMULARIO -->
            <div class="p-6 sm:p-7 space-y-5">
                
                <!-- ALERTAS -->
                <?php if (!empty($mensajeError)): ?>
                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-center space-x-2.5 animate-fade-in">
                        <i class="fa-solid fa-circle-exclamation text-rose-600"></i>
                        <div><?= $mensajeError ?></div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($mensajeExito)): ?>
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center space-x-2.5 animate-fade-in">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <div><?= $mensajeExito ?></div>
                    </div>
                <?php endif; ?>

                <!-- FORMULARIO LIMPIO -->
                <form action="index.php?accion=login_procesar" method="POST" class="space-y-4">
                    
                    <!-- Usuario (DNI) -->
                    <div>
                        <label for="usuario" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Usuario
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-user"></i>
                            </span>
                            <input type="text" name="usuario" id="usuario" required autofocus
                                   placeholder="Ingrese su usuario o DNI"
                                   class="touch-target w-full pl-10 rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-800 placeholder-slate-400 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                            Contraseña
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="password" required 
                                   placeholder="••••••••"
                                   class="touch-target w-full pl-10 rounded-xl border border-slate-300 px-3.5 py-2.5 text-slate-800 placeholder-slate-400 focus:border-[#005B94] focus:ring-2 focus:ring-[#005B94]/20 transition text-sm">
                        </div>
                    </div>

                    <!-- Botón de Inicio de Sesión -->
                    <button type="submit" 
                            class="touch-target w-full mt-2 py-3 rounded-xl bg-[#005B94] hover:bg-[#0A2540] active:scale-[0.98] text-white font-bold text-sm shadow-md transition flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-arrow-right-to-bracket text-[#2EC4B6]"></i>
                        <span>Iniciar Sesión</span>
                    </button>
                </form>

            </div>
        </div>

        <!-- PIE DE PÁGINA -->
        <div class="text-center text-xs text-slate-400">
            © <?= date('Y') ?> <strong>Podología Ana Ramírez</strong> • Todos los derechos reservados
        </div>

    </div>

</body>
</html>
