<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use App\Models\{User, Comment, Attachment, Task, TaskUser};

class UserTest extends TestCase
{
    use RefreshDatabase;

    //----------- Database Testing --------------//
    
    /**
     * Teste les colonnes de la table correspondant au modèle User
     *
     * @return void
     */
    public function testUserTableHasExpectedColumns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', 
                [
                    "id", "name", "email", "email_verified_at", "password",
                    "created_at", "updated_at"
                ]
            ), 1
        );
    }

    /**
     * Vérifie que le modèle est bien sauvé dans la base de donnée
     * 
     * @return void
     */
    public function testUserIsSavedInDatabase() {
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', $user->attributesToArray());
    }

    /**
     * Vérifie qu'un utilisateur peut être supprimé, même s'il est réferencé par d'autres modèles
     * 
     * @depends testUserIsSavedInDatabase
     * @return void
     */
    public function testUserIsDeletedFromDatabase() {

        $user = User::factory()->create(); 
        Task::factory()->create(["user_id" => $user->id]);
        Attachment::factory()->create(["user_id" => $user->id]);
        Comment::factory()->create(["user_id" => $user->id]);
        TaskUser::factory()->create(["user_id" => $user->id]);

        $user->delete(); 
        $this->assertDeleted($user);
    }

    //---------------- Relationship Testing -----------------------//


    /**
     * Teste la relation entre le modèle User et le modèle Task, pour vérifier la relation de 'propriété' (appartenance)
     *
     * @return void
     */
    public function testUserHasManyOwnedTask()
    {
        $nb = 3; 
        $user = User::factory()->hasOwnedTasks($nb)->create();
        $this->assertEquals($user->ownedTasks->count(), $nb);
        //On verifie que la relation d'appartenance n'utilise pas le pivot
        $this->assertNull($user->ownedTasks->first()->pivot);
    }


    /**
     * Teste la relation entre le modèle User et le modèle Task 
     *
     * @return void
     */
    public function testUserHasManyTasks()
    {
        $nb         = 3; 
        $user       = User::factory()
                        ->hasTasks($nb)
                        ->create();

        // Test 1 : Le nombre de tâches de l'utilisateur est bien égal à $nb (le jeu de données fourni dans la fonction).
        $this->assertEquals($nb, $user->tasks->count());

        // Test 2: Les tâches dont l'utilisateur est propriétaire sont bien liés à la tâche et sont bien une collection.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->tasks);
    }



    /**
     * Teste la relation entre le modèle Task et le modèle User 
     *
     * @return void
     */
    public function testUserHasManyAssignedTasks()
    {
        $nb         = 3; 
        $user       = User::factory()
                        ->hasAttached(
                            Task::factory()->count($nb), 
                            ['assigned' => true]
                        )
                        ->create();

        // test 1: Le nombre de tâches  assignés à l'utilisateurs est bien égal à $nb (le jeu de données fourni dans la fonction).
        $this->assertEquals($nb, $user->assignedTasks->count());

        // Test 2: Les tâches  assignés sont bien liés à l'utilisateur et sont bien une collection.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->assignedTasks);
    }




    /**
     * Teste la relation entre le modèle Task et le modèle User 
     *
     * @depends testUserHasManyTasks
     * @return void
     */
    public function testUserHasManyTasksNotAssignedByDefault()
    {
        $nb         = 3; 
        $user       = User::factory()
                        ->hasTasks($nb)
                        ->create();
        // Test 1 : Aucune des tâches n'est assigné par défaut à l'utilisateur
        $this->assertEquals(0, $user->assignedTasks->count());
    }

    /**
     * Teste la relation entre le modèle User et le modèle Comment 
     *
     * @return void
     */
    public function testUserHasManyComments()
    {
        
        $user    = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
   
        // Méthode 1 : le commentaire existe dans la liste des commentaires de l'utilisateur
        $this->assertTrue($user->comments->contains($comment));
        
        // Méthode 2: Le nombre de commentaires de l'utilisateur est bien égal à 1 (le jeu de données fourni dans la fonction).
        $this->assertEquals(1, $user->comments->count());

        // Méthode 3: Les commentaires sont bien liés à l'utilisateur et sont bien une collection.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->comments);
    }

    /**
     * Teste la relation entre le modèle User et le modèle Attachment 
     *
     * @return void
     */
    public function testUserHasManyAttachments()
    {
        $user    = User::factory()->create();
        $attachment = Attachment::factory()->create(['user_id' => $user->id]);
   
        // Méthode 1 : la pièce jointe existe dans la liste des pièces jointes de l'utilisateur
        $this->assertTrue($user->attachments->contains($attachment));
        
        // Méthode 2: Le nombre de pièces jointes de l'utilisateur est bien égal à 1 (le jeu de données fourni dans la fonction).
        $this->assertEquals(1, $user->attachments->count());

        // Méthode 3: Les pièces jointes sont bien liés à l'utilisateur et sont bien une collection.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->attachments);
    }

}
