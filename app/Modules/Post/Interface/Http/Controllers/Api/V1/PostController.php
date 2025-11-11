<?php
namespace App\Modules\Post\Interface\Http\Controllers\Api\V1;
use App\Application\Post\Commands\CreatePostCommand;
use App\Application\Post\Data\CreatePostData;
use App\Application\Post\Handlers\CreatePostHandler;
use App\Core\Interface\Controllers\BaseController as ControllersBaseController;
use Illuminate\Http\Request;

class PostController extends ControllersBaseController
{


    public function index()
    {
        return response()->json(['message' => 'List of posts']);
        return response()->json(['posts' => []]);
    }

    public function show($id)
    {
        return response()->json(['post' => ['id' => $id]]);
    }

    public function store(CreatePostData $request, CreatePostHandler $createPostHandler)
    {

    
        $createPostCommand = new CreatePostCommand(
            $request->title,
            $request->subtitle,
            $request->body,
            $request->is_featured,
            $request->excerpt,
            $request->author_id,
            $request->published_at,
            $request->meta,
            $request->tag_ids,
            $request->category_ids,
        );



        $post = $createPostHandler->handle($createPostCommand);
        return response()->json(['post' => $createPostCommand], 201);

       
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        // Simulate post update
        $post = array_merge(['id' => $id], $data);

        return response()->json(['post' => $post]);
    }

    public function destroy($id)
    {
        // Simulate post deletion
        return response()->json(null, 204);
    }
}