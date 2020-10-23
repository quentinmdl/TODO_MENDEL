<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use App\Models\{User, Task, Comment};
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{


    use RefreshDatabase;

    /**
     * Teste les colonnes de la table correspondant au modèle Comment
     *
     * @return void
     */
    public function testCommentTableHasExpectedColumns()
    {
        $this->assertTrue(
            Schema::hasColumns('comments', ["id", "user_id", "task_id", "text", "created_at", "updated_at"]), 1
        );
    }

    /**
     * Vérifie que la contrainte de clé étrangère pour l'utilisateur est bien prise en compte dans la table liée au modèle Comment
     *
     * @return void
     */
    public function testCommentDatabaseThrowsIntegrityConstraintExceptionOnNonExistingUserId() 
    {
        $this->expectException("Illuminate\Database\QueryException");
        $this->expectExceptionCode(23000);
        Comment::factory()->create(['user_id' =>1000]);
    }

    /**
     * Vérifie que la contrainte de clé étrangère pour la tâche est bien prise en compte dans la table liée au modèle Comment
     *
     * @return void
     */
    public function testCommentDatabaseThrowsIntegrityConstraintExceptionOnNonExistingTaskId() 
    {
        $this->expectException("Illuminate\Database\QueryException");
        $this->expectExceptionCode(23000);
        Comment::factory()->create(['task_id' =>1000]);
    }

    /**
     * Vérifie que le modèle est bien sauvé dans la base de donnée
     * 
     * @return void
     */
    public function testCommentIsSavedInDatabase() {
        $comment = Comment::factory()->create();
        $this->assertDatabaseHas('comments', $comment->attributesToArray());
    }

    /**
     * Vérifie que le modèle est bien supprimé de la base de donnée
     * 
     * @depends testCommentIsSavedInDatabase
     * @return void
     */
    public function testCommentIsDeletedFromDatabase() {
        $comment = Comment::factory()->create();
        $comment->delete();
        $this->assertDeleted($comment);
    }



    //---------------- Relationship Testing -----------------------//

    /**
     * Teste la relation entre le modèle Comment et le modèle User
     *
     * @return void
     */
    public function testCommentBelongsToAnUser()
    {
        $user    = User::factory()->create(); 
        $comment    = Comment::factory()->create(['user_id' => $user->id]);
        
   
        // Méthode 1 : l'uitlisateur associé à la pièce jointe est un bien une instance de la classe User
        $this->assertInstanceOf(User::class, $comment->user);
        
        // Méthode 2: Le nombre d'utilisateur auquels est associée la pièce jointe est bien égal à 1
        $this->assertEquals(1, $comment->user()->count());

    }


    /**
     * Teste la relation entre le modèle Comment et le modèle Task
     *
     * @return void
     */
    public function testCommentBelongsToATask()
    {
        $task    = Task::factory()->create(); 
        $comment    = Comment::factory()->create(['task_id' => $task->id]);
        
   
        // Méthode 1 : la tâche associée à la pièce jointe est un bien une instance de la classe Task
        $this->assertInstanceOf(Task::class, $comment->task);
        
        // Méthode 2: Le nombre de tâche auquelles est associée la pièce jointe est bien égal à 1
        $this->assertEquals(1, $comment->task()->count());

    }



}