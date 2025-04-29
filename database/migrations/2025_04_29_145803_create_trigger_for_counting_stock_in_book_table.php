<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::unprepared('
                CREATE TRIGGER decrease_stock_on_peminjaman 
                AFTER INSERT ON peminjaman
                FOR EACH ROW
                BEGIN
                    IF NEW.status = "dipinjam" THEN
                        UPDATE buku 
                        SET stok = stok - 1
                        WHERE id = NEW.buku_id;
                    END IF;
                END
            ');

            DB::unprepared('
                CREATE TRIGGER increase_stock_on_pengembalian
                AFTER UPDATE ON peminjaman
                FOR EACH ROW 
                BEGIN
                    IF NEW.status = "dikembalikan" AND OLD.status = "dipinjam" THEN
                        UPDATE buku
                        SET stok = stok + 1
                        WHERE id = NEW.buku_id;
                    END IF;
                END
            ');
        } else {
            // SQLite syntax
            DB::unprepared('
                CREATE TRIGGER decrease_stock_on_peminjaman 
                AFTER INSERT ON peminjaman
                WHEN NEW.status = "dipinjam"
                BEGIN
                    UPDATE buku 
                    SET stok = stok - 1
                    WHERE id = NEW.buku_id;
                END
            ');

            DB::unprepared('
                CREATE TRIGGER increase_stock_on_pengembalian
                AFTER UPDATE ON peminjaman
                WHEN NEW.status = "dikembalikan" AND OLD.status = "dipinjam"
                BEGIN
                    UPDATE buku
                    SET stok = stok + 1
                    WHERE id = NEW.buku_id;
                END
            ');
        }
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS decrease_stock_on_peminjaman');
        DB::unprepared('DROP TRIGGER IF EXISTS increase_stock_on_pengembalian');
    }
};
