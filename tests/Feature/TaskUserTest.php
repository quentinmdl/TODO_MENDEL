<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Task, TaskUser};

class TaskUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste les colonnes de la table correspondant au modèle TaskUser
     *
     * @return void
     */
    public function testTaskUserTableHasExpectedColumns()
    {
        $this->assertTrue(
            Schema::hasColumns('task_user', 
                [
                    "id", "user_id", "task_id", "assigned", 
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
    public function testTaskUserIsSavedInDatabase() {
        $task_user = TaskUser::factory()->create();
        $this->assertDatabaseHas('task_user', $task_user->attributesToArray());
    }


    /**
     * Vérifie que le modèle est bien supprimé de la base de donnée
     *
     * @depends testTaskUserIsSavedInDatabase
     * @return void
     */
    public function testTaskUserIsDeletedFromDatabase() {

        $task_user = TaskUser::factory()->create(); 
        $task_user->delete(); 
        $this->assertDeleted('task_user', $task_user->attributesToArray());
    }
        
    /**
     * Vérifie que la contrainte de clé étrangère pour l'utilisateur est bien prise en compte dans la table liée au modèle TaskUser
     *
     * @return void
     */
    public function testTaskUserDatabaseThrowsIntegrityConstraintExceptionOnNonExistingUserId() 
    {
        $this->expectException("Illuminate\Database\QueryException");
        $this->expectExceptionCode(23000);
        TaskUser::factory()->create(['user_id' =>1000]);
    }
    
    /**
     * Vérifie que la contrainte de clé étrangère pour la tâche est bien prise en compte dans la table liée au modèle TaskUser
     *
     * @return void
     */
    public function testTaskUserDatabaseThrowsIntegrityConstraintExceptionOnNonExistingTaskId() 
    {
        $this->expectException("Illuminate\Database\QueryException");
        $this->expectExceptionCode(23000);
        TaskUser::factory()->create(['task_id' =>1000]);
    }

    /**
     * Vérifie que la contrainte d'unicité est bien prise en compte dans la table liée au modèle TaskUser
     *
     * @return void
     */
    public function testTaskUserDatabaseThrowsIntegrityConstraintExceptionOnDuplicateEntry() 
    {

        $this->expectException("Illuminate\Database\QueryException");
        $this->expectExceptionCode(23000);
        $user    = User::factory()->create(); 
        $task    = Task::factory()->create(['user_id' => $user->id]);
        $taskUser = TaskUser::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);
        $taskUser2 = TaskUser::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);
    }

    //---------------- Relationship Testing -----------------------//

    /**
     * Teste la relation entre le modèle Comment et le modèle User
     *
     * @return void
     */
    public function testTaskUserBelongsToAnUser()
    {
        $user    = User::factory()->create(); 
        $task_user    = TaskUser::factory()->create(['user_id' => $user->id]);
        
   
        // Méthode 1 : l'uitlisateur associé à la pièce jointe est un bien une instance de la classe User
        $this->assertInstanceOf(User::class, $task_user->user);
        
        // Méthode 2: Le nombre d'utilisateur auquels est associée la pièce jointe est bien égal à 1
        $this->assertEquals(1, $task_user->user()->count());

    }


    /**
     * Teste la relation entre le modèle Comment et le modèle Task
     *
     * @return void
     */
    public function testTaskUserBelongsToATask()
    {
        $task    = Task::factory()->create(); 
        $task_user    = TaskUser::factory()->create(['task_id' => $task->id]);
        
   
        // Méthode 1 : la tâche associée à la pièce jointe est un bien une instance de la classe Task
        $this->assertInstanceOf(Task::class, $task_user->task);
        
        // Méthode 2: Le nombre de tâche auquelles est associée la pièce jointe est bien égal à 1
        $this->assertEquals(1, $task_user->task()->count());

    }


}
