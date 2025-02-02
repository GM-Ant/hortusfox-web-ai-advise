<div class="columns">
    <div class="column is-1"></div>

    <div class="column is-10">
        <h1>{{ __('app.chat') }}</h1>

        <h2 class="smaller-headline">{{ __('app.chat_hint') }}</h2>

        @include('flashmsg.php')

        <div class="margin-vertical">
            <form id="frmSendChatMessage" method="POST" action="{{ url('/chat/add') }}">
                @csrf

                <div class="field has-addons">
                    <div class="control is-stretched">
                        <textarea class="textarea is-input-dark" name="message" onkeypress="window.vue.handleChatInput();"></textarea>
                    </div>
                </div>

                <div class="control">
                    <a class="button is-success" href="javascript:void(0);" onclick="document.getElementById('frmSendChatMessage').submit();">{{ __('app.send') }}</a>
                </div>
            </form>
        </div>

        @if (app('chat_showusers', false))
            <div class="chat-user-list" id="chat-user-list"></div>
        @endif

        <div class="chat" id="chat">
            <div class="chat-message chat-typing-indicator">
                <div class="chat-message-content">
                    <span><i id="chat-typing-circle-1" class="fas fa-circle"></i></span>
                    <span><i id="chat-typing-circle-2" class="fas fa-circle"></i></span>
                    <span><i id="chat-typing-circle-3" class="fas fa-circle"></i></span>
                </div>
            </div>

            @if (isset($messages))
                @foreach ($messages as $message)
                    @if (!$message->get('sysmsg'))
                        <div class="chat-message {{ ($message->get('userId') == $user->get('id')) ? 'chat-message-right' : '' }}">
                            <div class="chat-message-user">
                                <div class="is-inline-block" style="color: {{ UserModel::getChatColorForUser($message->get('userId')) }};">{{ UserModel::getNameById($message->get('userId')) }}</div>
                                @if (ChatViewModel::handleNewMessage($user->get('id'), $message->get('id')))
                                    <div class="chat-message-new">{{ __('app.new') }}</div>
                                @endif
                            </div>

                            <div class="chat-message-content">
                                <pre>{!! UtilsModule::purify(UtilsModule::translateURLs($message->get('message'))) !!}</pre>
                            </div>

                            <div class="chat-message-info">
                                {{ (new Carbon($message->get('created_at')))->diffForHumans() }}
                            </div>
                        </div>
                    @else
                        <?php $isNewMessage = ChatViewModel::handleNewMessage($user->get('id'), $message->get('id')); ?>

                        <div class="system-message">
                            <div class="system-message-left {{ ($isNewMessage) ? 'system-message-left-new' : '' }}">
                                <div class="system-message-context" title="{{ date('Y-m-d H:i:s', strtotime($message->get('created_at'))) }}">{{ (($message->get('userId')) ? UserModel::getNameById($message->get('userId')) : 'System') . ' @ ' . (new Carbon(strtotime($message->get('created_at'))))->diffForHumans() }}</div>
                                
                                <div class="system-message-content">{!! UtilsModule::purify($message->get('message')) !!}</div>
                            </div>

                            @if ($isNewMessage)
                                <div class="system-message-right">
                                    <div class="system-message-new chat-message-new">{{ __('app.new') }}</div>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>

    <div class="column is-1"></div>
</div>