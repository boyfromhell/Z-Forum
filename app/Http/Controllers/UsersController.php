<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserLikedPosts;
use App\UserMessage;
use App\ActivityLog;
use Carbon\Carbon;
use App\Thread;
use App\User;
use App\Post;
use Cache;
use Auth;
use DB;

class UsersController extends Controller
{
	private function get_user_liked_posts(int $user_id) {
		return DB::table('user_liked_posts')
			->join('posts', 'posts.id', '=', 'user_liked_posts.post_id')->where('posts.user_id', $user_id)
			->get()
			->unique();
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		// Make it possible to go to /user/1 or /user/john but the latter is bound to the 'user_show' route
		if (User::find($id) || User::where('username', $id)->first()) {
			$user = User::find($id) ?? User::where('username', $id)->first();

			if (logged_in() && auth()->user()->id !== $user->id) {
				ActivityLog::create([
					'user_id' 	   => auth()->user()->id,
					'task'	  	   => __('visited'),
					'performed_on' => json_encode(['table' => 'users', 'id' => $user->id]),
				]);
			}

			$posts = $this->get_user_liked_posts($user->id);

			return view('user.single', [
				'posts_with_likes' => $posts,
				'user' => $user,
			]);
		} else {
			return view('errors.404');
		}
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

	public function push_status()
	{
		if (Auth::check()) {
			$expiresAt = Carbon::now()->addMinutes(3);
			Cache::put('user-online-' . Auth::user()->id, true, $expiresAt);

			// Update last seen row
			$user = Auth::user();
			$user->last_seen = Carbon::now();
			$user->save();
		}
	}

	public function show_activity(Request $request, $id)
	{
		// Make it possible to go to /user/1 or /user/john but the latter is bound to the 'user_show' route
		if (User::find($id) || User::where('username', $id)->first()) {
			$activities = ActivityLog::where('user_id', $user->id)->paginate(settings_get('posts_per_page'));
			$user = User::find($id) ?? User::where('username', $id)->first();
			$posts = $this->get_user_liked_posts($user->id);

			if (!count($activities)) {
				$activities = false;
			}

			return view('user.activity', [
				'posts_with_likes' => $posts,
				'activities' 	   => $activities,
				'user'			   => $user,
			]);
		} else {
			return view('errors.404');
		}
	}

	public function show_posts(Request $request, $id)
	{
		// Make it possible to go to /user/1 or /user/john but the latter is bound to the 'user_show' route
		if (User::find($id) || User::where('username', $id)->first()) {
			$userPosts = Post::where('user_id', $user->id)->paginate(settings_get('posts_per_page'));
			$user = User::find($id) ?? User::where('username', $id)->first();
			$posts = $this->get_user_liked_posts($user->id);

			return view('user.posts', [
				'posts_with_likes' => $posts,
				'posts' 		   => $userPosts,
				'user' 			   => $user,
			]);
		} else {
			return view('errors.404');
		}
	}

	public function show_likes(Request $request, $id)
	{
		// Make it possible to go to /user/1 or /user/john but the latter is bound to the 'user_show' route
		if (User::find($id) || User::where('username', $id)->first()) {
			$user  = User::find($id) ?? User::where('username', $id)->first();
			$likes = $user->likes()->distinct('post_id')->paginate(settings_get('posts_per_page'));
			$posts = $this->get_user_liked_posts($user->id);

			return view('user.likes', [
				'posts_with_likes' => $posts,
				'likes' 		   => $likes,
				'user' 			   => $user,
			]);
		} else {
			return view('errors.404');
		}
	}

	public function show_threads(Request $request, $id)
	{
		// Make it possible to go to /user/1 or /user/john but the latter is bound to the 'user_show' route
		if (User::find($id) || User::where('username', $id)->first()) {
			$user = User::find($id) ?? User::where('username', $id)->first();
			$posts = $this->get_user_liked_posts($user->id);
			
			return view('user.threads', [
				'posts_with_likes' => $posts,
				'threads' 		   => Thread::where('user_id', $user->id)->paginate(settings_get('posts_per_page')),
				'user' 			   => $user,
			]);
		} else {
			return view('errors.404');
		}
	}

    public function show_messages(Request $request, $id)
	{
		// Make it possible to go to /user/1 or /user/john but the latter is bound to the 'user_show' route
		if (User::find($id) || User::where('username', $id)->first()) {
			$user = User::find($id) ?? User::where('username', $id)->first();
			$posts = $this->get_user_liked_posts($user->id);
            $messages = UserMessage::where('author_id', $user->id)
                ->orWhere('recipient_id', $user->id)
                ->distinct('id')
                ->orderByDesc('created_at')
                ->paginate(settings_get('posts_per_page'));
			
			return view('user.messages', [
				'posts_with_likes' => $posts,
				'messages' 		   => $messages,
				'user' 			   => $user,
			]);
		} else {
			return view('errors.404');
		}
	}
}