<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use App\Models\{Attachment, User, Task};
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachmentTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Teste les colonnes de la table correspondant au modèle Attachment
     *
     * @return void
     */
    public function testAttachmentTableHasExpectedColumns()
    {
        $this->assertTrue(
            Schema::hasColumns('attachments', 
                [
                    "id", "user_id", "task_id", "file", "filename",
                    "size", "type", "created_at", "updated_at"
                ]
            ), 1
        );
    }

    /**
     * Vérifie que la contrainte de clé étrangère pour l'utilisateur est bien prise en compte dans la table liée au modèle Attachment
     *
     * @return void
     */
    public function testAttachmentDatabaseThrowsIntegrityConstraintExceptionOnNonExistingUserId() 
    {
        $this->expectException("Illuminate\Database\QueryException");
        $this->expectExceptionCode(23000);
        Attachment::factory()->create(['user_id' =>1000]);
    }

    /**
     * Vérifie que la contrainte de clé étrangère pour la tâche est bien prise en compte dans la table liée au modèle Attachment
     *
     * @return void
     */
    public function testAttachmentDatabaseThrowsIntegrityConstraintExceptionOnNonExistingTaskId() 
    {
        $this->expectException("Illuminate\Database\QueryException");
        $this->expectExceptionCode(23000);
        Attachment::factory()->create(['task_id' =>1000]);
    }

    /**
     * Vérifie que le modèle est bien sauvé dans la base de donnée
     * 
     * @return void
     */
    public function testAttachmentIsSavedInDatabase() {
        $attachment    = Attachment::factory()->create();
        $this->assertDatabaseHas('attachments', $attachment->attributesToArray());
    }

    /**
     * Vérifie que le modèle est bien supprimé de la base de donnée
     * 
     * @depends testAttachmentIsSavedInDatabase
     * @return void
     */
    public function testAttachmentIsDeletedFromDatabase() {
        $attachment = Attachment::factory()->create();
        $attachment->delete();
        $this->assertDeleted($attachment);
    }


    /**
     * Teste la relation entre le modèle Attachment et le modèle Task
     *
     * @return void
     */
    public function testAttachmentBelongsToATask()
    {
        $task    = Task::factory()->create(); 
        $attachment    = Attachment::factory()->create(['task_id' => $task->id]);
        
   
        // Méthode 1 : la tâche associée à la pièce jointe est un bien une instance de la classe Task
        $this->assertInstanceOf(Task::class, $attachment->task);
        
        // Méthode 2: Le nombre de tâche auquelles est associée la pièce jointe est bien égal à 1
        $this->assertEquals(1, $attachment->task()->count());

    }


    /**
     * Teste la relation entre le modèle Attachment et le modèle User
     *
     * @return void
     */
    public function testAttachmentBelongsToAnUser()
    {
        $user    = User::factory()->create(); 
        $attachment    = Attachment::factory()->create(['user_id' => $user->id]);
        
   
        // Méthode 1 : l'utilisateur associé à la pièce jointe est un bien une instance de la classe User
        $this->assertInstanceOf(User::class, $attachment->user);
        
        // Méthode 2: Le nombre d'utilisateur auquels est associée la pièce jointe est bien égal à 1
        $this->assertEquals(1, $attachment->user()->count());

    }


    




}
