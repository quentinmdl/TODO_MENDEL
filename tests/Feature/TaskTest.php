<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use App\Models\{Category, User, Task, Comment, Attachment, TaskUser};
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Teste les colonnes de la table correspondant au modèle Task
     *
     * @return void
     */
    public function testTaskTableHasExpectedColumns()
    {
        $this->assertTrue(
            Schema::hasColumns('tasks', ["id", "title", "description", "due_date", "state", "category_id", "user_id", "created_at", "updated_at"]), 1
        );
    }


    /**
     * Teste la relation entre le modèle Task et le modèle User, pour vérifier qu'il y a bien un propriétaire 
     *
     * @return void
     */
    public function testTaskBelongsToAnOwner()
    {
        $user    = User::factory()->create(); 
        $task    = Task::factory()->create(['user_id' => $user->id]);
        
   
        // Méthode 1 : le priopriétaire de la tâche est un bien une instance de la classe User
        $this->assertInstanceOf(User::class, $task->owner);
        
        // Méthode 2: Le nombre de propiétaires de la tâche est bien égal à 1
        $this->assertEquals(1, $task->owner()->count());

    }

    /**
     * Teste la relation entre le modèle Task et le modèle Category, pour vérifier qu'il y a bien une catégorie assignée à la tâche 
     *
     * @return void
     */
    public function testTaskBelongsToACategory()
    {
        $category = Category::factory()->create(); 
        $task     = Task::factory()->create(['category_id' => $category->id]);
   
        // Méthode 1 : la catégorie de la tâche est un bien une instance de la classe Category
        $this->assertInstanceOf(Category::class, $task->category);
        
        // Méthode 2: Le nombre de catégorie de la tâche est bien égal à 1
        $this->assertEquals(1, $task->category()->count());

    }


    /**
     * Teste la relation entre le modèle Task et le modèle User 
     *
     * @return void
     */
    public function testTaskHasManyUsers()
    {
        $nb         = 3; 
        $task       = Task::factory()
                        ->hasUsers($nb)
                        ->create();
            
        // Test 1 : Le nombre d'utilisateur de la tâche est bien égal à $nb (le jeu de données fourni dans la fonction).
        $this->assertEquals($nb, $task->users->count());

        // Test 2: Les utilisateurs sont bien liés à la tâche et sont bien une collection.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $task->users);
    }

    /**
     * Teste la relation entre le modèle Task et le modèle User 
     *
     * @return void
     */
    public function testTaskHasManyAssignedUsers()
    {
        $nb         = 3; 
        $task       = Task::factory()
                        ->hasAttached(
                            User::factory()->count($nb), 
                            ['assigned' => true]
                        )
                        ->create();
        
        // test 1: Le nombre d'utilisateurs assignés à la tâche est bien égal à $nb (le jeu de données fourni dans la fonction).
        $this->assertEquals($nb, $task->assignedUsers->count());

        // Test 2: Les utilisateurs assignés sont bien liés à la tâche et sont bien une collection.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $task->assignedUsers);
    }



    /**
     * Teste la relation entre le modèle Task et le modèle User 
     *
     * @depends testTaskHasManyUsers
     * @return void
     */
    public function testTaskHasManyUsersNotAssignedByDefault()
    {
        $nb         = 3; 
        $task       = Task::factory()
                        ->hasUsers($nb)
                        ->create();
        // Test 1 : Aucun des utilisateurs n'est assigné par défaut à la tâche
        $this->assertEquals(0, $task->assignedUsers->count());
    }

    /**
     * Teste la relation entre le modèle Task et le modèle Comment 
     *
     * @return void
     */
    public function testTaskHasManyComments()
    {
        
        $task    = Task::factory()->create();
        $comment = Comment::factory()->create(['task_id' => $task->id]);
   
        // Méthode 1 : le commentaire existe dans la liste des commentaires de la tâche
        $this->assertTrue($task->comments->contains($comment));
        
        // Méthode 2: Le nombre de commentaires de la tâche est bien égal à 1 (le jeu de données fourni dans la fonction).
        $this->assertEquals(1, $task->comments->count());

        // Méthode 3: Les commentaires sont bien liés à la tâche et sont bien une collection.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $task->comments);
    }

    /**
     * Teste la relation entre le modèle Task et le modèle Attachment 
     *
     * @return void
     */
    public function testTaskHasManyAttachments()
    {
        $task    = Task::factory()->create();
        $attachment = Attachment::factory()->create(['task_id' => $task->id]);
   
        // Méthode 1 : la pièce jointe existe dans la liste des pièces jointes de la tâche
        $this->assertTrue($task->attachments->contains($attachment));
        
        // Méthode 2: Le nombre de pièces jointes de la tâche est bien égal à 1 (le jeu de données fourni dans la fonction).
        $this->assertEquals(1, $task->attachments->count());

        // Méthode 3: Les pièces jointes sont bien liés à la tâche et sont bien une collection.
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $task->attachments);
    }

    /**
     * Vérifie que le modèle est bien sauvé dans la base de donnée
     * 
     * @return void
     */
    public function testTaskIsSavedInDatabase() {
        $task = Task::factory()->create();
        $this->assertDatabaseHas('tasks', $task->attributesToArray());
    }


    /**
     * Vérifie qu'une tâche peut être supprimée, même si elle est réferencée par d'autres modèles
     *
     * @depends testTaskIsSavedInDatabase
     * @return void
     */
    public function testTaskIsDeletedFromDatabase() {

        $task = Task::factory()->create(); 
        Attachment::factory()->create(["task_id" => $task->id]);
        Comment::factory()->create(["task_id" => $task->id]);
        TaskUser::factory()->create(["task_id" => $task->id]);

        $task->delete(); 
        $this->assertDeleted($task);
    }
   
    
    /**
     * Vérifie que la contrainte de clé étrangère pour l'utilisateur est bien prise en compte dans la table liée au modèle Task
     *
     * @return void
     */
    public function testTaskDatabaseThrowsIntegrityConstraintExceptionOnNonExistingUserId() 
    {
        $this->expectException("Illuminate\Database\QueryException");
        $this->expectExceptionCode(23000);
        Task::factory()->create(['user_id' =>1000]);
    }
    
    /**
     * Vérifie que la contrainte de clé étrangère pour la catégorie est bien prise en compte dans la table liée au modèle Task
     *
     * @return void
     */
    public function testTaskDatabaseThrowsIntegrityConstraintExceptionOnNonExistingCategoryId() 
    {
        $this->expectException("Illuminate\Database\QueryException");
        $this->expectExceptionCode(23000);
        Task::factory()->create(['category_id' =>1000]);
    }
    
    /**
     * Vérifie la présence du modèle pivot entre les utilisateurs et la table liée au modèle Task
     *
     * @return void
     */
    public function testTaskAsPivotClassForUsers() {
        $task       = Task::factory()
                        ->hasUsers(1)
                        ->create();
        $this->assertInstanceOf('App\Models\TaskUser', $task->users()->first()->pivot);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\Pivot', $task->users()->first()->pivot);
    }


}
