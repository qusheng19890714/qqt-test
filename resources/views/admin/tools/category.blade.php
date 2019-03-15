<div class="btn-group" data-toggle="buttons">
    <select name="category_id" class="category-select">
        <option value="0">全部</option>
        @foreach($options as $k=>$v)
            <option value="{{$k}}">{{$v}}</option>
        @endforeach

    </select>


</div>