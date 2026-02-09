@props(['url'])
<tr>
<td class="header">
<a  href="{{ route('catalogoMin') }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('images/SEA-SWINWEAR-LOGO.webp') }}" class="logo" alt="Laravel Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
