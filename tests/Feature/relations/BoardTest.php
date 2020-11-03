<?php

namespace Tests\Feature\Relations;

use Tests\TestCase;
use App\Models\{User, Board};
use Illuminate\Foundation\Testing\RefreshDatabase;

class BoardTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Teste la relation entre le modèle Board et le modèle User, pour vérifier qu'il y a bien un propriétaire 
     *
     * @return void
     */
    public function testBoardBelongsToAnOwner()
    {
        $user    = User::factory()->create(); 
        $board    = Board::factory()->create(['user_id' => $user->id]);
        
   
        // Méthode 1 : le priopriétaire de la board est un bien une instance de la classe User
        $this->assertInstanceOf(User::class, $board->owner);
        
        // Méthode 2: Le nombre de propiétaires de la board est bien égal à 1
        $this->assertEquals(1, $board->owner()->count());
    }



    /**
     * Teste la relation entre le modèle Board et le modèle User (les particants du board)
     *
     * @return void
     */
    public function testBoardBelongsToManyUsers()
    {
        $nb         = 3; 
        $board       = Board::factory()
                        ->hasUsers($nb)
                        ->create();
            
        // Test 1 : Le nombre d'utilisateur de la board est bien égal à $nb (le jeu de données fourni dans la fonction).
        $this->assertEquals($nb, $board->users->count());

        // Test 2: Les utilisateurs sont bien liés à la board et sont bien une collection.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $board->users);
    }


    /**
     * Teste la relation entre le modèle Board et le modèle Task
     * 
     * @return void
     */
    public function testBoardHasManyTasks()
    {
        $nb         = 3; 
        $board       = Board::factory()
                        ->hasTasks($nb)
                        ->create();
            
        // Test 1 : Le nombre de tâche de la board est bien égal à $nb (le jeu de données fourni dans la fonction).
        $this->assertEquals($nb, $board->tasks->count());

        // Test 2: Les tâches sont bien liés à la board et sont bien une collection.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $board->tasks);
    }
    
    /**
     * Vérifie la présence du modèle pivot entre les utilisateurs et la table liée au modèle Board
     *
     * @return void
     */
    public function testBoardHasPivotClassForUsers() {
        $board       = Board::factory()
                        ->hasUsers(1)
                        ->create();
        $this->assertInstanceOf('App\Models\BoardUser', $board->users()->first()->pivot);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\Pivot', $board->users()->first()->pivot);
    }


}
