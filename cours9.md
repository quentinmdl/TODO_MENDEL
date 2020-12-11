# Ce qu'il y a avait à faire :

Finir les contrôleurs TaskController, BoardController, et faire TaskUSer.

Il faudrait pouvoir faire en sorte que seul le propriétaire d'un board puisse ajouter/supprimer des utilisateurs participant au board : https://laravel.com/docs/8.x/authorization (vous serez surtout attentif aux policies)

On fera aussi en sorte de finir nos todo : à savoir quand on ajoute une tache à un board, il faut vérifier que le board auquel appartient la tâche appartient aussi à l'utilisateur qui fait cet ajout : fonction `store` et `storeFromBoard`.

## TaskController


On constate que les tâches sont toujours associées à un board. Ainsi, il serait préférable d'obtenir le board depuis la route (dans l'url). 

On peut donc définir la route suivante : 
`Route::resource("/boards/{board}/tasks", TaskController::class);`

Nous devrons rajouter un paramètre `Board $board` à chacune des fonctions du contrôleur. 
Les routes et fonctions createFromBoard ne seront plus nécessaires. 


On créé la vue `boards.task.index`, qui renvoie affiche toutes les tâches de la board

On a fini le contrôleur pour les tâches `TaskController`, ainsi que les vues associées. 

On va s'occuper des mettre en oeuvre les règles suivantes: 
 1. Pour les boards     
    - un utilisateur ne peut voir que les boards auxquelles il participe. 
    - Seul le propriétaire d'un board peut le modifier ou le supprimer
    - tous les utilisateurs connecté peuvent en créer un 
 2. Pour les tâches d'un board
    - tous les participants du board peuvent en créer une 
    - tous les participants du board peuvent s'assigner une tâche
    
    - seul les utilisateurs assignés peuvent modifier le status ??? À voir comment faire. 


Pour cela, nous allons utiliser les policies. 
pour créer une policy pour le modèle board : 
`php artisan make:policy BoardPolicy --model=Board`

Un fichier de policy contient des fonctions : 
    - `viewAny` à utiliser dans la fonction `index` du contrôleur
    - `view` à utiliser dans la fonction `show` du contrôleur
    - `create` à utiliser dans la fonction `create` et `store` du contrôleur
    - `update` à utiliser dans la fonction `edit` et `update` du contrôleur
    - `delete` à utiliser dans la fonction `delete` du contrôleur
    

Pour les utiliser, on a 3 possibilités
 - en utilisant une fonction `authorize` du contrôleur (dans un contrôleur) : 
   ```php
   public function create(Request $request)
    {
        $this->authorize('create', Board::class);

        // The current user can create Board ...
    } 
    ```
 - En utilisant le modèle User dans un contrôleur ou une vue : 
    ```php 
    public function store(Request $request)
    {
        if ($request->user()->cannot('create', Board::class)) { // Il existe les méthode can et cannot
            abort(403);
        }

        // Create the post...
    }
    ```
- Via les middleware `can` et `cannot` (dans les routes et les vues)
    ```php
        Route::put('boards/{board}', [BoardController::class, 'update'])->middleware('can:update,board');
    ```
    cf : https://laravel.com/docs/8.x/authorization#via-middleware



On peut aussi utiliser les policies dans les templates blade : en utilisant des directive `@can('update', $board) ... @endcan` : 
    ```html
        @can('view', $board)
        <a href="{{route('boards.show', $board)}}">Voir</a>
        @endcan
        @can('update', $board)
        <a href="{{route('boards.edit', $board)}}">Edit</a>
        @endcan
    ```
    cf https://laravel.com/docs/8.x/authorization#via-blade-templates


On peut se contenter d'initialiser les autorisations dans el constructeur de notre contrôleur si celui-ci est bien un `resource controller` lié à un modèle :
    ```php
        public function __construct()
        {
            /* 
                Cette fonction gère directement les autorisations pour chacune des méthodes du contrôleur 
                en fonction des méthodes de BoardPolicy(viewAny, view, update, ....)
                https://laravel.com/docs/8.x/authorization#authorizing-resource-controllers
                
            */
            $this->authorizeResource(Board::class, 'board'); 
        }
    ```
    
    cf https://laravel.com/docs/8.x/authorization#authorizing-resource-controllers