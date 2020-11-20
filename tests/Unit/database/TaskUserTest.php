<?php

namespace Tests\Unit\Database;

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
                    "id", "user_id", "task_id", /*"assigned", */
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
        TaskUser::factory()->create(['user_id' =>0]);
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
        TaskUser::factory()->create(['task_id' =>0]);
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
        $task    = Task::factory()->create();
        $taskUser = TaskUser::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);
        $taskUser2 = TaskUser::factory()->create(['task_id' => $task->id, 'user_id' => $user->id]);
    }

}
