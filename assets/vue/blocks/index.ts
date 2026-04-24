import type { Component } from 'vue'
import BlockText from './BlockText.vue'
import BlockImage from './BlockImage.vue'
import BlockSlider from './BlockSlider.vue'
import BlockVideo from './BlockVideo.vue'
import BlockCta from './BlockCta.vue'
import BlockDivider from './BlockDivider.vue'

const blockMap: Record<string, Component> = {
  text:    BlockText,
  image:   BlockImage,
  slider:  BlockSlider,
  video:   BlockVideo,
  cta:     BlockCta,
  divider: BlockDivider,
}

export default blockMap
