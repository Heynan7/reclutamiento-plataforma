<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('name', 100);
            $table->string('email', 100)->unique();

            // Teléfono (opcional)
            $table->string('phone', 20)->nullable();

            // Verificación de email
            $table->timestamp('email_verified_at')->nullable();

            // Autenticación
            $table->string('password');

            // Rol
            $table->enum('role', ['admin', 'user'])->default('user');

            // CV opcional
            $table->string('cv_file')->nullable();

            // Avatar / Provider
            $table->string('avatar')->nullable();
            $table->string('provider')->default('local');

            // Token "recuérdame"
            $table->rememberToken();

            // Timestamps
            $table->timestamps();
        });

        /**
         * 🚀 Crear administradores por defecto
         */
        $admin1Password = 'WrAdmin#2025'; // Contraseña generada
        $admin2Password = 'Melvin#2025'; // Contraseña generada

        DB::table('users')->insert([
            [
                'name' => 'Waleska Rodríguez',
                'email' => 'infowrconsultorias@gmail.com',
                'phone' => '36977610',
                'password' => Hash::make($admin1Password),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Melvin Gómez',
                'email' => 'melvingomez1000@gmail.com',
                'phone' => null,
                'password' => Hash::make($admin2Password),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Mostrar las contraseñas generadas en consola (solo una vez)
        echo "\n=============================\n";
        echo "✅ Administradores creados:\n";
        echo "1️⃣ Waleska Rodríguez → infowrconsultorias@gmail.com\n";
        echo "   Contraseña: {$admin1Password}\n\n";
        echo "2️⃣ Melvin Gómez → melvingomez1000@gmail.com\n";
        echo "   Contraseña: {$admin2Password}\n";
        echo "=============================\n\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
